<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = ReturnRequest::with(['user', 'order'])->latest();

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $counts = [
            'all' => ReturnRequest::count(),
            'pending' => ReturnRequest::where('status', 'pending')->count(),
            'approved' => ReturnRequest::where('status', 'approved')->count(),
            'rejected' => ReturnRequest::where('status', 'rejected')->count(),
        ];

        $reasonCounts = ReturnRequest::select('reason', DB::raw('count(*) as total'))
            ->groupBy('reason')
            ->pluck('total', 'reason')
            ->toArray();

        $requests = $query->paginate(20)->withQueryString();

        $totalApprovedRefund = ReturnRequest::where('return_requests.status', 'approved')
            ->join('orders', 'return_requests.order_id', '=', 'orders.id')
            ->sum('orders.total');
            
        $totalPendingRefund = ReturnRequest::where('return_requests.status', 'pending')
            ->join('orders', 'return_requests.order_id', '=', 'orders.id')
            ->sum('orders.total');

        return view('admin.returns.index', compact('requests', 'counts', 'status', 'reasonCounts', 'totalApprovedRefund', 'totalPendingRefund'));
    }

    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $updateData = ['status' => $request->status];

        // Generate tracking code if approved
        if ($request->status == 'approved' && empty($returnRequest->tracking_code)) {
            $updateData['tracking_code'] = 'RET-' . strtoupper(substr(uniqid(), -6));
        }

        $returnRequest->update($updateData);

        // Update order status
        $order = $returnRequest->order;
        if ($order) {
            if ($request->status == 'approved') {
                $order->update(['status' => 'returned']);
            } else {
                $order->update(['status' => 'finished']);
            }
        }

        return back()->with('success', 'Đã cập nhật trạng thái yêu cầu trả hàng thành công.');
    }
}
