<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsIssue;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(protected GHNService $ghn) {}

    public function index(Request $request)
    {
        $sort = $request->get('sort') === 'oldest' ? 'oldest' : 'newest';

        $query = Order::with(['user', 'items']);

        // Lọc theo ngày tạo (Thẻ 'Đơn hôm nay' trên Dashboard)
        if ($request->get('date') === 'today') {
            $query->whereDate('created_at', now()->today());
        }

        // Lọc trạng thái đơn hàng
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereIn('status', ['completed', 'finished']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Lọc trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Lọc đơn COD chưa thu tiền (Thẻ 'Tiền COD chưa thu' trên Dashboard)
        if ($request->boolean('cod_unpaid')) {
            $query->where('payment_method', 'cod')
                ->where('payment_status', '!=', 'paid')
                ->whereNotIn('status', ['cancelled', 'returned']);
        }

        // Lọc đơn giao chậm > 3 ngày (Khu vực 'Đơn giao chậm' trên Dashboard)
        if ($request->boolean('shipping_delayed')) {
            $query->whereIn('status', ['shipping', 'delivering'])
                ->where('updated_at', '<', now()->subDays(3));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('id', $s)
                    ->orWhere('recipient_name', 'like', "%{$s}%")
                    ->orWhere('recipient_phone', 'like', "%{$s}%")
                    ->orWhere('ghn_order_code', 'like', "%{$s}%");
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at')->orderBy('id');
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $orders = $query->paginate(15)->withQueryString();
        $counts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'picked_up' => Order::where('status', 'picked_up')->count(),
            'delivering' => Order::where('status', 'delivering')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'finished' => Order::where('status', 'finished')->count(),
            'returning' => Order::where('status', 'returning')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts', 'sort'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,preparing,picked_up,delivering,completed,finished,returning,returned,cancelled',
            'payment_status' => 'required|in:unpaid,paid',
            'ghn_order_code' => 'nullable|string|max:100',
        ]);

        return DB::transaction(function () use ($order, $data) {
            $lockedOrder = Order::lockForUpdate()->findOrFail($order->id);
            $old = $lockedOrder->status;
            $oldPayment = $lockedOrder->payment_status;

            // Khi chuyển sang delivering mà chưa có mã GHN → thử tạo vận đơn
            if ($data['status'] === 'delivering' && empty($lockedOrder->ghn_order_code) && empty($data['ghn_order_code'])) {
                $created = $this->tryCreateGhnOrder($lockedOrder);
                if ($created) {
                    $data['ghn_order_code'] = $created;
                }
            }

            // Tự động chuyển Đã thanh toán nếu trạng thái là Giao thành công / Hoàn thành
            if (in_array($data['status'], ['completed', 'finished'])) {
                $data['payment_status'] = 'paid';
            }

            // Xử lý luồng tồn kho TỰ ĐỘNG dựa theo thiết kế Kiến trúc Kho WMS
            if ($old !== 'completed' && $old !== 'finished' && in_array($data['status'], ['completed', 'finished'])) {
                // 1. Đơn giao thành công -> Trừ reserved_stock và tạo Phiếu Xuất Kho tự động
                $lockedOrder->load('items');
                $issue = GoodsIssue::create([
                    'order_id' => $lockedOrder->id,
                    'user_id' => auth()->id(),
                    'type' => 'sale',
                    'status' => 'completed',
                    'note' => 'Hệ thống tự động xuất kho do Đơn hàng #'.$lockedOrder->id.' giao thành công',
                ]);

                foreach ($lockedOrder->items as $item) {
                    if ($item->product_id) {
                        $product = Product::lockForUpdate()->find($item->product_id);
                        if ($product) {
                            $product->decrement('reserved_stock', $item->quantity);
                            $issue->details()->create([
                                'product_id' => $product->id,
                                'quantity' => $item->quantity,
                            ]);
                            InventoryTransaction::create([
                                'product_id' => $product->id,
                                'type' => 'out',
                                'quantity' => $item->quantity,
                                'reference_type' => GoodsIssue::class,
                                'reference_id' => $issue->id,
                                'note' => 'Xuất bán Đơn hàng #'.$lockedOrder->id,
                            ]);
                        }
                    }
                }
            } elseif (! in_array($old, ['cancelled', 'returning', 'returned']) && $data['status'] === 'cancelled') {
                // 2. Admin Hủy đơn -> Trả lại reserved_stock về stock bán được
                $lockedOrder->load('items');
                foreach ($lockedOrder->items as $item) {
                    if ($item->product_id) {
                        $product = Product::lockForUpdate()->find($item->product_id);
                        if ($product) {
                            $product->decrement('reserved_stock', $item->quantity);
                            $product->increment('stock', $item->quantity);

                            InventoryTransaction::create([
                                'product_id' => $product->id,
                                'type' => 'in',
                                'quantity' => $item->quantity,
                                'reference_type' => Order::class,
                                'reference_id' => $lockedOrder->id,
                                'note' => 'Admin hủy Đơn #'.($lockedOrder->order_code ?? $lockedOrder->id).' - hoàn lại tồn kho bán',
                            ]);
                        }
                    }
                }
            }

            $lockedOrder->update([
                'status' => $data['status'],
                'payment_status' => $data['payment_status'],
                'ghn_order_code' => $data['ghn_order_code'] ?? $lockedOrder->ghn_order_code,
            ]);

            return back()->with('success', "Đã cập nhật đơn #{$lockedOrder->id}: {$old} → {$lockedOrder->status} | Thanh toán: {$lockedOrder->payment_status}.");
        });
    }

    protected function tryCreateGhnOrder(Order $order): ?string
    {
        try {
            $order->load('items');
            $items = $order->items->map(fn ($i) => [
                'name' => $i->product_name,
                'quantity' => $i->quantity,
                'weight' => 500,
            ])->toArray();

            $result = $this->ghn->createOrder([
                'to_name' => $order->recipient_name,
                'to_phone' => $order->recipient_phone,
                'to_address' => $order->address_detail,
                'to_ward_code' => $order->ward_code,
                'to_district_id' => $order->district_id,
                'weight' => max(500, count($items) * 500),
                'length' => 20,
                'width' => 20,
                'height' => 15,
                'service_type_id' => 2,
                'payment_type_id' => $order->payment_method === 'cod' ? 2 : 1,
                'required_note' => 'CHOXEMHANGKHONGTHU',
                'items' => $items,
                'cod_amount' => $order->payment_method === 'cod' ? (int) $order->total : 0,
                'client_order_code' => 'LS-'.$order->id,
            ]);

            if (! empty($result['success']) && ! empty($result['data']['order_code'])) {
                return $result['data']['order_code'];
            }

            Log::warning('GHN createOrder failed for admin order', [
                'order_id' => $order->id,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('GHN createOrder exception: '.$e->getMessage());
        }

        return null;
    }
}
