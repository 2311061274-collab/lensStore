<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị giao diện Dashboard Admin
     */
    public function index()
    {
        $metrics = $this->calculateMetrics();

        return view('admin.dashboard', $metrics);
    }

    /**
     * API đồng bộ dữ liệu Dashboard thời gian thực (không cần F5 trang)
     */
    public function apiData(Request $request): JsonResponse
    {
        $metrics = $this->calculateMetrics();

        // Chuẩn hóa dữ liệu danh sách sản phẩm cảnh báo kho
        $lowStockData = $metrics['lowStock']->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku ?: '—',
                'brand' => $p->brand ?: '—',
                'category_name' => $p->category ? $p->category->name : 'Chưa phân loại',
                'stock' => (int) $p->stock,
                'is_out_of_stock' => $p->stock <= 0,
                'status_label' => $p->stock <= 0 ? 'Hết hàng' : 'Sắp hết',
                'image_url' => $p->image_url,
                'edit_url' => route('admin.products.edit', $p),
                'show_url' => route('admin.products.show', $p),
            ];
        });

        // Chuẩn hóa dữ liệu danh sách đơn giao chậm
        $stuckShippingData = $metrics['stuckShipping']->map(function ($o) {
            $daysInTransit = (int) ($o->updated_at ? $o->updated_at->diffInDays(now()) : 0);
            $daysDelayed = max(1, $daysInTransit - 3);

            return [
                'id' => $o->id,
                'order_code' => $o->order_code ?: ('#'.$o->id),
                'recipient_name' => $o->recipient_name,
                'recipient_phone' => $o->recipient_phone,
                'ghn_order_code' => $o->ghn_order_code ?: '—',
                'created_at_formatted' => $o->created_at ? $o->created_at->format('d/m/Y') : '',
                'status_label' => $o->status_label,
                'days_in_transit' => $daysInTransit,
                'days_delayed' => $daysDelayed,
                'show_url' => route('admin.orders.show', $o),
            ];
        });

        return response()->json([
            'success' => true,
            'timestamp' => now()->timestamp,
            'lastUpdated' => $metrics['lastUpdated'],
            'counts' => [
                'ordersToday' => $metrics['ordersToday'],
                'ordersWeek' => $metrics['ordersWeek'],
                'ordersMonth' => $metrics['ordersMonth'],
                'pendingOrders' => $metrics['pendingOrders'],
                'preparingOrders' => $metrics['preparingOrders'],
                'shippingOrders' => $metrics['shippingOrders'],
                'completedOrders' => $metrics['completedOrders'],
                'cancelledOrders' => $metrics['cancelledOrders'],
                'pendingReturns' => $metrics['pendingReturns'],
                'pendingRefunds' => $metrics['pendingRefunds'],
                'pendingCodAmount' => $metrics['pendingCodAmount'],
                'totalRefunded' => $metrics['totalRefunded'],
                'revenueToday' => $metrics['revenueToday'],
                'revenueWeek' => $metrics['revenueWeek'],
                'revenueMonth' => $metrics['revenueMonth'],
                'newCustomers' => $metrics['newCustomers'],
                'outOfStockCount' => $metrics['outOfStockCount'],
                'lowStockCount' => $metrics['lowStockCount'],
                'totalStockAlerts' => $metrics['totalStockAlerts'],
                'stuckShippingCount' => $metrics['stuckShippingCount'],
            ],
            'formatted' => [
                'revenueToday' => number_format($metrics['revenueToday'], 0, ',', '.').' ₫',
                'revenueWeek' => number_format($metrics['revenueWeek'], 0, ',', '.').' ₫',
                'revenueMonth' => number_format($metrics['revenueMonth'], 0, ',', '.').' ₫',
                'pendingCodAmount' => number_format($metrics['pendingCodAmount'], 0, ',', '.').' ₫',
                'totalRefunded' => number_format($metrics['totalRefunded'], 0, ',', '.').' ₫',
            ],
            'charts' => [
                'labels' => $metrics['labels'],
                'values' => $metrics['values'],
                'brandPie' => $metrics['brandPie'],
            ],
            'lowStock' => $lowStockData,
            'stuckShipping' => $stuckShippingData,
        ]);
    }

    /**
     * Tính toán tổng hợp số liệu thống kê chuẩn từ database
     */
    private function calculateMetrics(): array
    {
        $isAdmin = auth()->check() ? auth()->user()->role === 'admin' : false;
        $today = now()->startOfDay();
        $week = now()->subDays(6)->startOfDay();
        $month = now()->startOfMonth();

        // Tự động chuyển đơn "Giao hàng thành công" sang "Đã hoàn thành" sau 10 ngày
        Order::where('status', 'completed')
            ->where('updated_at', '<', now()->subDays(10))
            ->update(['status' => 'finished']);

        $ordersToday = Order::where('created_at', '>=', $today)->count();
        $ordersWeek = Order::where('created_at', '>=', $week)->count();
        $ordersMonth = Order::where('created_at', '>=', $month)->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $preparingOrders = Order::where('status', 'preparing')->count();
        $shippingOrders = Order::whereIn('status', ['delivering', 'shipping'])->count();
        $completedOrders = Order::whereIn('status', ['completed', 'finished'])->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Chỉ số Đổi trả & Hoàn tiền
        $pendingReturns = ReturnRequest::where('status', 'pending')->count();
        $pendingRefunds = ReturnRequest::where('status', 'approved')->where('refund_status', '!=', 'refunded')->count();
        $totalRefunded = (int) ReturnRequest::where('refund_status', 'refunded')->sum('refund_amount');

        // Tiền COD chưa thu (các đơn COD chưa thu tiền và chưa bị hủy/trả hàng)
        $pendingCodAmount = (int) Order::where('payment_method', 'cod')
            ->where('payment_status', '!=', 'paid')
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('total');

        $validStatuses = ['finished', 'completed'];

        $revenueToday = 0;
        $revenueWeek = 0;
        $revenueMonth = 0;
        $revenueSeries = [];
        $brandPie = [];

        if ($isAdmin) {
            // Doanh thu thực nhận: Chỉ tính các đơn đã hoàn thành/giao thành công và ĐÃ THANH TOÁN (paid)
            $revenueToday = (int) Order::where('created_at', '>=', $today)
                ->whereIn('status', $validStatuses)->where('payment_status', 'paid')->sum('total');
            $revenueWeek = (int) Order::where('created_at', '>=', $week)
                ->whereIn('status', $validStatuses)->where('payment_status', 'paid')->sum('total');
            $revenueMonth = (int) Order::where('created_at', '>=', $month)
                ->whereIn('status', $validStatuses)->where('payment_status', 'paid')->sum('total');

            $revenueSeries = Order::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(total) as revenue')
            )
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->whereIn('status', $validStatuses)
                ->where('payment_status', 'paid')
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('revenue', 'day')
                ->toArray();

            $brandPie = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereIn('orders.status', $validStatuses)
                ->where('orders.payment_status', 'paid')
                ->select('products.brand', DB::raw('SUM(order_items.quantity) as qty'))
                ->groupBy('products.brand')
                ->orderByDesc('qty')
                ->limit(8)
                ->get()
                ->map(fn ($r) => [
                    'brand' => $r->brand ?: 'Khác',
                    'qty' => (int) $r->qty,
                ])
                ->filter(fn ($r) => $r['qty'] > 0)
                ->values()
                ->toArray();
        }

        // Cảnh báo tồn kho: Phân biệt rõ Hết hàng (stock <= 0) và Sắp hết hàng (0 < stock < 3)
        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $lowStockCount = Product::where('stock', '>', 0)->where('stock', '<', 3)->count();
        $totalStockAlerts = $outOfStockCount + $lowStockCount;

        $lowStock = Product::with('category')
            ->where('stock', '<', 3)
            ->where('stock', '>=', 0)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // Đơn giao chậm: các đơn đang giao hàng quá 3 ngày
        $stuckShippingCount = Order::whereIn('status', ['shipping', 'delivering'])
            ->where('updated_at', '<', now()->subDays(3))
            ->count();

        $stuckShipping = Order::with('user')
            ->whereIn('status', ['shipping', 'delivering'])
            ->where('updated_at', '<', now()->subDays(3))
            ->orderBy('updated_at', 'asc')
            ->limit(8)
            ->get();

        $newCustomers = $isAdmin
            ? User::where('role', 'customer')->where('created_at', '>=', $week)->count()
            : null;

        // Điền đầy đủ 14 ngày cho biểu đồ doanh thu
        $labels = [];
        $values = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');
            $values[] = (int) ($revenueSeries[$d] ?? 0);
        }

        $lastUpdated = now()->format('H:i:s d/m/Y');

        return compact(
            'isAdmin',
            'ordersToday',
            'ordersWeek',
            'ordersMonth',
            'pendingOrders',
            'preparingOrders',
            'shippingOrders',
            'completedOrders',
            'cancelledOrders',
            'pendingReturns',
            'pendingRefunds',
            'pendingCodAmount',
            'totalRefunded',
            'revenueToday',
            'revenueWeek',
            'revenueMonth',
            'labels',
            'values',
            'brandPie',
            'outOfStockCount',
            'lowStockCount',
            'totalStockAlerts',
            'lowStock',
            'stuckShippingCount',
            'stuckShipping',
            'newCustomers',
            'lastUpdated'
        );
    }
}
