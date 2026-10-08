<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderActionController extends Controller
{
    // Xác nhận đã nhận hàng
    public function confirmReceived(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status !== 'completed') return back()->with('error', 'Đơn hàng chưa giao thành công!');

        $order->update(['status' => 'finished']);
        return back()->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng!');
    }

    // Form yêu cầu trả hàng
    public function returnRequestForm(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status !== 'completed') return back()->with('error', 'Chỉ có thể trả hàng khi đã giao thành công.');
        
        // Kiểm tra xem đã yêu cầu chưa
        if (ReturnRequest::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'Bạn đã gửi yêu cầu trả hàng cho đơn này rồi.');
        }

        return view('storefront.order_return', compact('order'));
    }

    // Xử lý lưu trả hàng
    public function returnRequestStore(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if ($order->status !== 'completed') return back()->with('error', 'Chỉ có thể trả hàng khi đã giao thành công.');

        $request->validate([
            'reason' => 'required|string',
            'note' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = 'uploads/returns/' . time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/returns'), $imagePath);
        }

        ReturnRequest::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'reason' => $request->reason,
            'note' => $request->note,
            'image' => $imagePath,
            'status' => 'pending'
        ]);

        $order->update(['status' => 'returning']);

        return redirect()->route('orders.show', $order->id)->with('success', 'Đã gửi yêu cầu trả hàng/hoàn tiền. Chúng tôi sẽ xử lý sớm nhất.');
    }

    // Form đánh giá
    public function reviewForm(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if (!in_array($order->status, ['completed', 'finished'])) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá khi đơn hàng đã hoàn tất.');
        }

        $order->load('items.product');
        return view('storefront.order_review', compact('order'));
    }

    // Xử lý lưu đánh giá
    public function reviewStore(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string',
        ]);

        foreach ($request->reviews as $rev) {
            // Check if reviewed
            if (!Review::where('order_id', $order->id)->where('product_id', $rev['product_id'])->exists()) {
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
