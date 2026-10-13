<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderActionController extends Controller
{
    // Xác nhận đã nhận hàng
    public function confirmReceived(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if ($order->status !== 'completed') {
            return back()->with('error', 'Đơn hàng chưa giao thành công!');
        }

        $order->update(['status' => 'finished']);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng!');
    }

    // Form yêu cầu trả hàng
    public function returnRequestForm(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (! in_array($order->status, ['completed', 'finished'])) {
            return back()->with('error', 'Chỉ có thể yêu cầu đổi trả khi đơn hàng đã giao thành công.');
        }

        // Kiểm tra xem đã có yêu cầu đang xử lý chưa
        if (ReturnRequest::where('order_id', $order->id)->whereIn('status', ['pending', 'approved', 'completed'])->exists()) {
            return back()->with('error', 'Bạn đã gửi yêu cầu trả hàng cho đơn này rồi.');
        }

        $order->load('items.product');

        return view('storefront.order_return', compact('order'));
    }

    // Xử lý lưu trả hàng
    public function returnRequestStore(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (! in_array($order->status, ['completed', 'finished'])) {
            return back()->with('error', 'Chỉ có thể yêu cầu đổi trả khi đơn hàng đã giao thành công.');
        }

        if (ReturnRequest::where('order_id', $order->id)->whereIn('status', ['pending', 'approved', 'completed'])->exists()) {
            return back()->with('error', 'Đơn hàng này đã có yêu cầu đổi trả đang được xử lý.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
            'video_url' => 'required|url|max:500',
            'bank_name' => 'required|string|max:100',
            'bank_account_holder' => 'required|string|max:100',
            'bank_account_number' => 'required|string|min:6|max:40',
        ], [
            'reason.required' => 'Vui lòng chọn lý do trả hàng.',
            'video_url.required' => 'Vui lòng dán liên kết video mở hàng từ Google Drive.',
            'video_url.url' => 'Định dạng đường dẫn liên kết video không hợp lệ.',
            'video_url.max' => 'Đường dẫn liên kết không được vượt quá 500 ký tự.',
            'bank_name.required' => 'Vui lòng nhập tên ngân hàng nhận tiền hoàn.',
            'bank_account_holder.required' => 'Vui lòng nhập tên chủ tài khoản ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
        ]);

        // Kiểm tra định dạng link Google Drive hợp lệ
        $videoUrl = trim($validated['video_url']);
        if (! str_contains($videoUrl, 'drive.google.com') && ! str_contains($videoUrl, 'docs.google.com')) {
            return back()->withInput()->withErrors([
                'video_url' => 'Đường dẫn video không hợp lệ. Vui lòng cung cấp liên kết chia sẻ từ Google Drive (ví dụ: https://drive.google.com/file/d/...).',
            ]);
        }

        try {
            DB::transaction(function () use ($validated, $order, $videoUrl) {
                // Khóa bản ghi đơn hàng để chống gửi trùng lặp đồng thời
                $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);

                if (ReturnRequest::where('order_id', $lockedOrder->id)->whereIn('status', ['pending', 'approved', 'completed'])->exists()) {
                    throw new \Exception('Đơn hàng này đã có yêu cầu đổi trả đang được xử lý.');
                }

                // Chuẩn hóa tên chủ tài khoản và số tài khoản
                $cleanHolder = mb_strtoupper(trim($validated['bank_account_holder']));
                $cleanNumber = preg_replace('/[^0-9A-Za-z]/', '', $validated['bank_account_number']);

                ReturnRequest::create([
                    'user_id' => Auth::id(),
                    'order_id' => $lockedOrder->id,
                    'reason' => $validated['reason'],
                    'note' => $validated['note'] ?? null,
                    'video_url' => $videoUrl,
                    'status' => 'pending',
                    'return_shipping_status' => 'pending_tracking',
                    'bank_name' => trim($validated['bank_name']),
                    'bank_account_holder' => $cleanHolder,
                    'bank_account_number' => $cleanNumber,
                    'refund_status' => 'pending',
                    'refund_amount' => (int) $lockedOrder->total,
                ]);

                $lockedOrder->update(['status' => 'returning']);
            });
        } catch (\Throwable $e) {
            Log::error('Create return request transaction failed: '.$e->getMessage());

            return back()->withInput()->with('error', $e->getMessage() ?: 'Có lỗi xảy ra khi tạo yêu cầu hoàn hàng. Vui lòng thử lại.');
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Đã gửi yêu cầu trả hàng / hoàn tiền thành công. LensStore sẽ đối chiếu video mở hàng và xử lý trong thời gian sớm nhất!');
    }

    // Form đánh giá
    public function reviewForm(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (! in_array($order->status, ['completed', 'finished'])) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá khi đơn hàng đã hoàn tất.');
        }

        $order->load('items.product');

        return view('storefront.order_review', compact('order'));
    }

    // Xử lý lưu đánh giá
    public function reviewStore(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string',
        ]);

        foreach ($request->reviews as $rev) {
            // Check if reviewed
            if (! Review::where('order_id', $order->id)->where('product_id', $rev['product_id'])->exists()) {
                Review::create([
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'product_id' => $rev['product_id'],
                    'rating' => $rev['rating'],
                    'comment' => $rev['comment'],
                ]);
            }
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
