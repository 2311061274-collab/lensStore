<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')
            ->where('user_id', Auth::id())
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return view('storefront.cart', compact('carts'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $request->quantity,
                'updated_at' => now(),
            ]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => 'Đã thêm sản phẩm vào giỏ hàng.',
                'cartCount' => $cartCount,
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function buyNow(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        Cart::where('user_id', Auth::id())->update(['is_selected' => false]);

        $cart = Cart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $request->quantity,
                'is_selected' => true,
                'updated_at' => now(),
            ]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'is_selected' => true,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('checkout.index'),
            ]);
        }

        return redirect()->route('checkout.index');
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Đã cập nhật số lượng.');
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function updateSelection(Request $request)
    {
        $request->validate([
            'selections' => 'required|array',
            'selections.*.id' => 'required|exists:carts,id',
            'selections.*.is_selected' => 'required|boolean',
        ]);

        foreach ($request->selections as $item) {
            Cart::where('id', $item['id'])
                ->where('user_id', Auth::id())
                ->update(['is_selected' => $item['is_selected']]);
        }

        return response()->json(['success' => true]);
    }

    public function checkout()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->where('is_selected', true)->get();
        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Bạn chưa chọn sản phẩm nào để thanh toán.');
        }

        return view('storefront.checkout', compact('carts'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer',
        ]);

        // Xử lý lưu đơn hàng ở đây (chưa yêu cầu trong task, chỉ cần form thanh toán)

        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('storefront.index')->with('success', 'Đặt hàng thành công!');
    }
}
