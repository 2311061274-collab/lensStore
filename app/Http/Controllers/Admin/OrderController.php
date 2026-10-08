<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(protected GHNService $ghn) {}

    public function index(Request $request)
    {
        $sort = $request->get('sort') === 'oldest' ? 'oldest' : 'newest';

        $query = Order::with(['user', 'items']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
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
            'all'        => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'preparing'  => Order::where('status', 'preparing')->count(),
            'picked_up'  => Order::where('status', 'picked_up')->count(),
            'delivering' => Order::where('status', 'delivering')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'finished'   => Order::where('status', 'finished')->count(),
            'returning'  => Order::where('status', 'returning')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
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

        $old = $order->status;
        $oldPayment = $order->payment_status;

        // Khi chuyển sang delivering mà chưa có mã GHN → thử tạo vận đơn
        if ($data['status'] === 'delivering' && empty($order->ghn_order_code) && empty($data['ghn_order_code'])) {
            $created = $this->tryCreateGhnOrder($order);
            if ($created) {
                $data['ghn_order_code'] = $created;
            }
        }

        // Tự động chuyển Đã thanh toán nếu trạng thái là Giao thành công / Hoàn thành
        if (in_array($data['status'], ['completed', 'finished'])) {
            $data['payment_status'] = 'paid';
            
            // Xóa luôn yêu cầu trả hàng (nếu đang pending) khỏi danh sách vì giao hàng đã thành công
            \App\Models\ReturnRequest::where('order_id', $order->id)
                ->where('status', 'pending')
                ->delete();
        }

        // Xử lý luồng tồn kho TỰ ĐỘNG dựa theo thiết kế Kiến trúc Kho
        if ($old !== 'completed' && $old !== 'finished' && in_array($data['status'], ['completed', 'finished'])) {
            // 1. Đơn giao thành công -> Xóa reserved_stock và tạo Phiếu Xuất Kho tự động
            $order->load('items.product');
            $issue = \App\Models\GoodsIssue::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'type' => 'sale',
                'status' => 'completed',
                'note' => 'Hệ thống tự động xuất kho do Đơn hàng #' . $order->id . ' giao thành công'
            ]);

            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->decrement('reserved_stock', $item->quantity);
                    $issue->details()->create([
                        'product_id' => $product->id,
                        'quantity' => $item->quantity
                    ]);
                    \App\Models\InventoryTransaction::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'reference_type' => \App\Models\GoodsIssue::class,
                        'reference_id' => $issue->id,
                        'note' => 'Xuất bán Đơn hàng #' . $order->id
                    ]);
                }
            }
        } elseif (!in_array($old, ['cancelled', 'returning', 'returned']) && $data['status'] === 'cancelled') {
            // 2. Admin Hủy đơn -> Trả lại reserved_stock về stock bán được
            $order->load('items.product');
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->decrement('reserved_stock', $item->quantity);
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        $order->update([
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'ghn_order_code' => $data['ghn_order_code'] ?? $order->ghn_order_code,
        ]);

        return back()->with('success', "Đã cập nhật đơn #{$order->id}: {$old} → {$order->status} | Thanh toán: {$order->payment_status}.");
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
                'client_order_code' => 'LS-' . $order->id,
            ]);

            if (!empty($result['success']) && !empty($result['data']['order_code'])) {
                return $result['data']['order_code'];
            }

            Log::warning('GHN createOrder failed for admin order', [
                'order_id' => $order->id,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('GHN createOrder exception: ' . $e->getMessage());
        }

        return null;
    }
}
