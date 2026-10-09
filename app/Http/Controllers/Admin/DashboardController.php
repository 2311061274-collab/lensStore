<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $isAdmin = auth()->user()->role === 'admin';
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
        $shippingOrders = Order::where('status', 'delivering')->count();

        $validStatuses = ['finished', 'completed'];

        $revenueToday = $revenueWeek = $revenueMonth = 0;
        $revenueSeries = [];
        $brandPie = [];

        if ($isAdmin) {
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

        $lowStock = Product::where('stock', '<', 3)
            ->where('stock', '>=', 0)
            ->orderBy('stock')
            ->limit(10)
            ->get();

        $stuckShipping = Order::with('user')
            ->where('status', 'shipping')
            ->where('updated_at', '<', now()->subDays(3))
            ->latest('updated_at')
            ->limit(8)
            ->get();

        $newCustomers = $isAdmin
            ? User::where('role', 'customer')->where('created_at', '>=', $week)->count()
            : null;

        // Fill missing days for chart
        $labels = [];
        $values = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');
            $values[] = (int) ($revenueSeries[$d] ?? 0);
        }

        return view('admin.dashboard', compact(
            'isAdmin',
            'ordersToday',
            'ordersWeek',
            'ordersMonth',
            'pendingOrders',
            'shippingOrders',
            'revenueToday',
            'revenueWeek',
            'revenueMonth',
            'labels',
            'values',
            'brandPie',
            'lowStock',
            'stuckShipping',
            'newCustomers'
        ));
    }
}
