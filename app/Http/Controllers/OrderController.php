<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Services\GHNService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct(
        protected GHNService $ghn,
        protected MomoService $momo,
    ) {}

    /** Danh sách đơn hàng của người dùng */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('storefront.orders', compact('orders'));
    }

    /** Chi tiết đơn hàng */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('storefront.order_detail', compact('order'));
    }

    /** Xử lý đặt hàng từ form checkout */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'province_id' => 'required|integer',
            'province_name' => 'required|string',
            'district_id' => 'required|integer',
            'district_name' => 'required|string',
            'ward_code' => 'required|string',
            'ward_name' => 'required|string',
            'address_detail' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer,momo',
            'shipping_fee' => 'required|integer|min:0',
            'voucher_code' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $carts = Cart::with('product')->where('user_id', Auth::id())->where('is_selected', true)->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Không có sản phẩm nào được chọn để thanh toán.');
        }

        $subtotal = $carts->sum(fn ($c) => $c->product->price * $c->quantity);
        $shippingFee = (int) $request->shipping_fee;
        $discountAmount = (float) ($request->discount_amount ?? 0);
        $total = max(0, $subtotal + $shippingFee - $discountAmount);

        // Xử lý voucher
        $voucherCode = null;
        if ($request->voucher_code) {
            $voucher = Voucher::where('code', $request->voucher_code)->first();
            if ($voucher && $voucher->isValidForOrder($subtotal)) {
                $voucherCode = $voucher->code;
                $discountAmount = $voucher->calculateDiscount($subtotal);
                $total = max(0, $subtotal + $shippingFee - $discountAmount);
                $voucher->increment('used_count');
            }
        }

        DB::beginTransaction();
        try {
            // Xác định trạng thái thanh toán ban đầu
            $paymentStatus = $request->payment_method === 'momo' ? 'pending' : 'pending';

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => 'TEMP_'.Str::random(8),
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'province_id' => $request->province_id,
                'province_name' => $request->province_name,
                'district_id' => $request->district_id,
                'district_name' => $request->district_name,
                'ward_code' => $request->ward_code,
                'ward_name' => $request->ward_name,
                'address_detail' => $request->address_detail,
                'shipping_fee' => $shippingFee,
                'subtotal' => $subtotal,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'voucher_code' => $voucherCode,
                'discount_amount' => $discountAmount,
                'status' => 'pending',
            ]);

            // Sinh mã đơn hàng chuyên nghiệp
            $order->update([
                'order_code' => 'LS'.now()->format('ymd').str_pad($order->id, 4, '0', STR_PAD_LEFT),
            ]);

            // Tạo order items
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product->name,
                    'quantity' => $cart->quantity,
                    'unit_price' => $cart->product->price,
                    'subtotal' => $cart->product->price * $cart->quantity,
                ]);

                // Xử lý giữ kho (Reserved Stock) với Row Lock chống race condition
                $product = Product::lockForUpdate()->find($cart->product_id);
                if (! $product || $product->stock < $cart->quantity) {
                    throw new \Exception('Sản phẩm '.($product ? $product->name : 'được chọn').' không đủ số lượng tồn kho khả dụng để đặt hàng.');
                }

                $product->decrement('stock', $cart->quantity);
                $product->increment('reserved_stock', $cart->quantity);

                // Ghi nhận lịch sử giữ kho
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $cart->quantity,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'note' => 'Giữ kho cho Đơn hàng #'.$order->order_code,
                ]);
            }

            // Xóa giỏ hàng những sản phẩm đã chọn
            Cart::where('user_id', Auth::id())->where('is_selected', true)->delete();

            DB::commit();

            // ─── Phân luồng thanh toán ───────────────────────────
            if ($request->payment_method === 'momo') {
                // Chuyển sang MoMo thanh toán — GHN sẽ được tạo sau khi MoMo callback thành công
                $momoResult = $this->momo->createPayment($order);

                if ($momoResult['success']) {
                    return redirect($momoResult['pay_url']);
                }

                // MoMo tạo link thất bại — vẫn giữ đơn hàng, cho phép thanh toán lại
                return redirect()->route('orders.show', $order->id)
                    ->with('warning', 'Đặt hàng thành công nhưng chưa thể mở trang thanh toán MoMo. Bạn có thể thanh toán lại trong chi tiết đơn hàng. Lỗi: '.($momoResult['message'] ?? ''));
            }

            // COD / Bank Transfer: Tạo vận đơn GHN ngay lập tức
            $ghnPayload = $this->buildGHNPayload($order, $carts);
            $ghnResult = $this->ghn->createOrder($ghnPayload);

            if ($ghnResult['success']) {
                $order->update(['ghn_order_code' => $ghnResult['data']['order_code'] ?? null]);
            } else {
                Log::warning('GHN order creation failed', ['order_id' => $order->id, 'ghn' => $ghnResult]);
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Đặt hàng thành công! Mã đơn hàng: #'.$order->id);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('processCheckout error', ['error' => $e->getMessage()]);

            return back()->with('error', 'Đã có lỗi xảy ra khi đặt hàng: '.$e->getMessage());
        }
    }

    /** Hủy đơn hàng an toàn với DB Transaction & Row Locks */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return DB::transaction(function () use ($order) {
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);

            if (! in_array($lockedOrder->status, ['pending', 'preparing'])) {
                return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái này hoặc đơn đã được xử lý.');
            }

            // Hủy trên GHN nếu có mã vận đơn
            if ($lockedOrder->ghn_order_code) {
                $this->ghn->cancelOrder($lockedOrder->ghn_order_code);
            }

            $lockedOrder->update(['status' => 'cancelled']);

            // Trả lại tồn kho (Từ Reserved về Available) an toàn
            $lockedOrder->load('items');
            foreach ($lockedOrder->items as $item) {
                if ($item->product_id) {
                    $prod = Product::lockForUpdate()->find($item->product_id);
                    if ($prod) {
                        $prod->decrement('reserved_stock', $item->quantity);
                        $prod->increment('stock', $item->quantity);

                        InventoryTransaction::create([
                            'product_id' => $prod->id,
                            'type' => 'in',
                            'quantity' => $item->quantity,
                            'reference_type' => Order::class,
                            'reference_id' => $lockedOrder->id,
                            'note' => 'Khách hủy đơn #'.($lockedOrder->order_code ?? $lockedOrder->id).' - hoàn tồn kho bán',
                        ]);
                    }
                }
            }

            return back()->with('success', 'Đã hủy đơn hàng thành công và hoàn trả số lượng tồn kho.');
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Private Helpers */
    /* ------------------------------------------------------------------ */

    private function buildGHNPayload(Order $order, $carts): array
    {
        $items = $carts->map(fn ($c) => [
            'name' => $c->product->name,
            'quantity' => $c->quantity,
            'price' => (int) $c->product->price,
            'weight' => 500, // gram mặc định mỗi sản phẩm
        ])->toArray();

        $totalWeight = array_sum(array_column($items, 'weight'));

        return [
            'to_name' => $order->recipient_name,
            'to_phone' => $order->recipient_phone,
            'to_address' => $order->address_detail,
            'to_ward_code' => $order->ward_code,
            'to_district_id' => $order->district_id,
            'weight' => $totalWeight,
            'service_type_id' => 2,
            'payment_type_id' => $order->payment_method === 'cod' ? 2 : 1,
            'required_note' => 'KHONGCHOXEMHANG',
            'cod_amount' => $order->payment_method === 'cod' ? $order->total : 0,
            'items' => $items,
        ];
    }
}
