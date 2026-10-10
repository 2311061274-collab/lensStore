<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $refundStatus = $request->query('refund_status');
        $query = ReturnRequest::with(['user', 'order', 'refundedBy'])->latest();

        if ($status && in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($refundStatus && in_array($refundStatus, ['pending', 'refunded', 'failed'])) {
            $query->where('refund_status', $refundStatus);
        }

        $counts = [
            'all' => ReturnRequest::count(),
            'pending' => ReturnRequest::where('status', 'pending')->count(),
            'approved' => ReturnRequest::where('status', 'approved')->count(),
            'rejected' => ReturnRequest::where('status', 'rejected')->count(),
            'refunded' => ReturnRequest::where('refund_status', 'refunded')->count(),
            'pending_refund' => ReturnRequest::where('status', 'approved')->where('refund_status', '!=', 'refunded')->count(),
        ];

        $reasonCounts = ReturnRequest::select('reason', DB::raw('count(*) as total'))
            ->groupBy('reason')
            ->pluck('total', 'reason')
            ->toArray();

        $requests = $query->paginate(20)->withQueryString();

        // Thống kê tài chính chuẩn xác: Tiền đã thực sự hoàn và Tiền đã duyệt nhưng đang chờ hoàn
        $totalRefunded = ReturnRequest::where('refund_status', 'refunded')->sum('refund_amount');
        $totalPendingRefund = ReturnRequest::where('status', 'approved')
            ->where('refund_status', '!=', 'refunded')
            ->sum('refund_amount');

        return view('admin.returns.index', compact(
            'requests',
            'counts',
            'status',
            'refundStatus',
            'reasonCounts',
            'totalRefunded',
            'totalPendingRefund'
        ));
    }

    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        return DB::transaction(function () use ($request, $returnRequest) {
            $locked = ReturnRequest::lockForUpdate()->findOrFail($returnRequest->id);
            $updateData = ['status' => $request->status];

            // Sinh mã vận đơn trả hàng khi chấp thuận
            if ($request->status === 'approved' && empty($locked->tracking_code)) {
                $updateData['tracking_code'] = 'RET-'.strtoupper(substr(uniqid(), -6));
                if (empty($locked->refund_amount) && $locked->order) {
                    $updateData['refund_amount'] = $locked->order->total;
                }
            }

            $locked->update($updateData);

            // Cập nhật trạng thái đơn hàng tương ứng
            $order = $locked->order;
            if ($order) {
                if ($request->status === 'approved') {
                    $order->update(['status' => 'returning']);
                } else {
                    $order->update(['status' => 'finished']);
                }
            }

            $actionText = $request->status === 'approved' ? 'Chấp nhận yêu cầu đổi trả' : 'Từ chối yêu cầu đổi trả';

            return back()->with('success', "Đã {$actionText} thành công cho đơn #{$locked->order_id}.");
        });
    }

    /**
     * Xác nhận đã hoàn tiền (COD / Chuyển khoản) cho khách hàng
     * Bảo đảm Idempotent, chống hoàn lặp và lưu vết người xác nhận
     */
    public function confirmRefund(Request $request, ReturnRequest $returnRequest)
    {
        return DB::transaction(function () use ($request, $returnRequest) {
            $locked = ReturnRequest::lockForUpdate()->findOrFail($returnRequest->id);

            // 1. Kiểm tra điều kiện tiên quyết: Yêu cầu phải được duyệt trước
            if ($locked->status !== 'approved') {
                return back()->with('error', 'Chỉ có thể xác nhận hoàn tiền cho yêu cầu trả hàng đã được duyệt chấp thuận.');
            }

            // 2. Chống hoàn lặp (Idempotent)
            if ($locked->refund_status === 'refunded') {
                return back()->with('error', 'Yêu cầu này đã được xác nhận hoàn tiền trước đó vào lúc '.($locked->refunded_at ? $locked->refunded_at->format('d/m/Y H:i') : '').'. Không thể hoàn tiền lần hai!');
            }

            $order = $locked->order;
            $maxAmount = $order ? (int) $order->total : (int) ($locked->refund_amount ?? 0);

            $validated = $request->validate([
                'refund_amount' => 'required|numeric|min:1000|max:'.max(1000, $maxAmount),
                'refund_method' => 'required|in:bank_transfer,momo,cash',
                'refund_reference' => 'nullable|string|max:100',
                'refund_note' => 'nullable|string|max:1000',
            ], [
                'refund_amount.required' => 'Vui lòng nhập số tiền hoàn thực tế.',
                'refund_amount.numeric' => 'Số tiền hoàn phải là chữ số hợp lệ.',
                'refund_amount.max' => 'Số tiền hoàn không được vượt quá tổng giá trị đơn hàng ('.number_format($maxAmount, 0, ',', '.').' ₫).',
            ]);

            $locked->update([
                'refund_status' => 'refunded',
                'refund_amount' => (int) $validated['refund_amount'],
                'refunded_at' => now(),
                'refunded_by' => auth()->id(),
                'refund_method' => $validated['refund_method'],
                'refund_reference' => $validated['refund_reference'] ?? null,
                'refund_note' => $validated['refund_note'] ?? null,
            ]);

            // Cập nhật trạng thái đơn hàng sang đã hoàn trả
            if ($order) {
                $order->update(['status' => 'returned']);
            }

            $recipientName = $locked->bank_account_holder ?: ($locked->user->name ?? 'khách hàng');

            return back()->with('success', 'Xác nhận hoàn tất! Đã ghi nhận hoàn tiền '.number_format($validated['refund_amount'], 0, ',', '.').' ₫ cho '.$recipientName.'.');
        });
    }
}
