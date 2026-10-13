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
        $search = trim($request->query('search', ''));
        $query = ReturnRequest::with(['user', 'order.items.product', 'refundedBy'])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('tracking_code', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_code', 'like', "%{$search}%")
                            ->orWhere('id', $search);
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

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
            'totalPendingRefund',
            'search'
        ));
    }

    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'tracking_code' => 'nullable|string|max:100',
        ]);

        return DB::transaction(function () use ($request, $returnRequest) {
            $locked = ReturnRequest::lockForUpdate()->findOrFail($returnRequest->id);
            $updateData = ['status' => $request->status];

            if ($request->status === 'approved') {
                if ($request->filled('tracking_code')) {
                    $updateData['tracking_code'] = trim($request->tracking_code);
                    $updateData['return_shipping_status'] = 'tracking_assigned';
                } elseif (empty($locked->tracking_code)) {
                    // Không tự tạo mã giả, hiển thị trạng thái chờ cấp mã
                    $updateData['return_shipping_status'] = 'pending_tracking';
                }

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

            return back()->with('success', "Đã {$actionText} thành công cho yêu cầu #{$locked->request_code} (Đơn #{$locked->order_id}).");
        });
    }

    /**
     * Cập nhật mã vận đơn trả hàng và trạng thái vận chuyển từ Admin / ĐVVC
     */
    public function updateTracking(Request $request, ReturnRequest $returnRequest)
    {
        $validated = $request->validate([
            'tracking_code' => 'nullable|string|max:100',
            'return_shipping_status' => 'required|in:pending_tracking,tracking_assigned,picking_up,delivering_back,received',
        ], [
            'tracking_code.max' => 'Mã vận đơn không được vượt quá 100 ký tự.',
            'return_shipping_status.required' => 'Vui lòng chọn trạng thái vận chuyển.',
            'return_shipping_status.in' => 'Trạng thái vận chuyển không hợp lệ.',
        ]);

        $trackingCode = ! empty($validated['tracking_code']) ? trim($validated['tracking_code']) : null;
        $shippingStatus = $validated['return_shipping_status'];

        // Nếu đã có mã vận đơn mà đang ở trạng thái 'pending_tracking' -> tự động chuyển sang 'tracking_assigned'
        if ($trackingCode && $shippingStatus === 'pending_tracking') {
            $shippingStatus = 'tracking_assigned';
        }

        $returnRequest->update([
            'tracking_code' => $trackingCode,
            'return_shipping_status' => $shippingStatus,
        ]);

        return back()->with('success', "Đã cập nhật mã vận đơn và trạng thái vận chuyển cho yêu cầu {$returnRequest->request_code}!");
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
