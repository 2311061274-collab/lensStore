<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'this_month');
        $from = $request->query('from');
        $to = $request->query('to');

        // Determine date range
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfDay();

        switch ($filter) {
            case 'today':
                $startDate = now()->startOfDay();
                break;
            case '7days':
                $startDate = now()->subDays(6)->startOfDay();
                break;
            case 'this_month':
                $startDate = now()->startOfMonth();
                break;
            case 'this_year':
                $startDate = now()->startOfYear();
                break;
            case 'custom':
                if ($from && $to) {
                    $startDate = Carbon::parse($from)->startOfDay();
                    $endDate = Carbon::parse($to)->endOfDay();
                }
                break;
        }

        $validStatuses = ['finished'];

        // 1. KPI Cards
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', $validStatuses)->where('payment_status', 'paid')->sum('total');

        $totalCompletedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', $validStatuses)->count();

        $allOrdersCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $aov = $totalCompletedOrders > 0 ? $totalRevenue / $totalCompletedOrders : 0;

        $badOrdersCount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['cancelled', 'returned'])->count();
        
        $returnCancelRate = $allOrdersCount > 0 ? ($badOrdersCount / $allOrdersCount) * 100 : 0;

        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])->count();

        // 2. Charts Data
        // Line Chart: Revenue by Date
        $revenueChartData = Order::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', $validStatuses)
            ->where('payment_status', 'paid')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('revenue', 'day')
            ->toArray();
            
        // Fill missing days for line chart
        $chartLabels = [];
        $chartValues = [];
        $periodDays = $startDate->diffInDays($endDate);
        if($periodDays > 60) $periodDays = 60; // Limit for UI

        for ($i = $periodDays; $i >= 0; $i--) {
            $dateObj = (clone $endDate)->subDays($i);
            $d = $dateObj->format('Y-m-d');
            $chartLabels[] = $dateObj->format('d/m');
            $chartValues[] = (int) ($revenueChartData[$d] ?? 0);
        }

        // Pie Chart: Revenue by Brand
        $brandPieData = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', $validStatuses)
            ->where('orders.payment_status', 'paid')
            ->select('products.brand', DB::raw('SUM(order_items.subtotal) as revenue'))
            ->groupBy('products.brand')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
            
        $pieLabels = [];
        $pieValues = [];
        foreach($brandPieData as $b) {
            $pieLabels[] = $b->brand ?: 'Khác';
            $pieValues[] = (int) $b->revenue;
        }

        // 3. Product Analytics
        $bestSellers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', $validStatuses)
            ->where('orders.payment_status', 'paid')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('sold')
            ->limit(10)
            ->get();

        $deadStock = Product::where('stock', '>', 0)
            ->whereDoesntHave('orderItems', function ($q) {
                $q->whereHas('order', fn ($o) => $o->where('created_at', '>=', now()->subDays(90))
                    ->whereIn('status', $validStatuses));
            })
            ->orderByDesc('stock')
            ->limit(10)
            ->get();
            
        $lowStockWarning = Product::where('stock', '<', 5)
            ->orderBy('stock')
            ->limit(10)
            ->get();

        // 4. Customer Analytics (Top Spenders)
        $topSpenders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', $validStatuses)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'filter', 'from', 'to', 'startDate', 'endDate',
            'totalRevenue', 'totalCompletedOrders', 'aov', 'returnCancelRate', 'newCustomers',
            'chartLabels', 'chartValues', 'pieLabels', 'pieValues',
            'bestSellers', 'deadStock', 'lowStockWarning', 'topSpenders'
        ));
    }

    public function exportOrders(Request $request): StreamedResponse
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $filename = "orders_{$from}_{$to}.csv";

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, [
                'ID', 'Ngày', 'Khách', 'SĐT', 'Tạm tính', 'Ship', 'Giảm', 'Tổng',
                'Thanh toán', 'Trạng thái', 'Mã GHN', 'Voucher',
            ]);

            Order::with('user')
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->orderBy('id')
                ->chunk(200, function ($orders) use ($out) {
                    foreach ($orders as $o) {
                        fputcsv($out, [
                            $o->id,
                            $o->created_at->format('Y-m-d H:i'),
                            $o->recipient_name,
                            $o->recipient_phone,
                            $o->subtotal,
                            $o->shipping_fee,
                            $o->discount_amount ?? 0,
                            $o->total,
                            $o->payment_method,
                            $o->status,
                            $o->ghn_order_code,
                            $o->voucher_code,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportRevenue(Request $request): StreamedResponse
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $filename = "revenue_{$from}_{$to}.csv";

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Ngày', 'Số đơn', 'Doanh thu', 'Giảm giá', 'Phí ship']);

            $rows = Order::select(
                    DB::raw('DATE(created_at) as day'),
                    DB::raw('COUNT(*) as orders'),
                    DB::raw('SUM(total) as revenue'),
                    DB::raw('SUM(COALESCE(discount_amount,0)) as discount'),
                    DB::raw('SUM(shipping_fee) as shipping')
                )
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->whereIn('status', $validStatuses)
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            foreach ($rows as $r) {
                fputcsv($out, [$r->day, $r->orders, $r->revenue, $r->discount, $r->shipping]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
