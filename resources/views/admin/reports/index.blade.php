@extends('layouts.admin')

@section('title', 'Báo cáo & Phân tích')

@section('content')
<div class="topbar" style="position: relative; z-index: 1000;">
    <div>
        <div class="breadcrumb">Quản lý / Thống kê</div>
        <h1>Báo cáo & Phân tích</h1>
    </div>
    
    <div style="display:flex; gap:10px; align-items:center;">
        <!-- Lọc thời gian toàn cục -->
        <form action="{{ route('admin.reports.index') }}" method="GET" id="filterForm" style="display:flex; gap:10px; align-items:center; margin:0;">
            <select name="filter" id="filterSelect" class="form-control" onchange="toggleCustomDates(this.value)" style="width:160px;">
                <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Hôm nay</option>
                <option value="7days" {{ $filter == '7days' ? 'selected' : '' }}>7 ngày qua</option>
                <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>Tháng này</option>
                <option value="this_year" {{ $filter == 'this_year' ? 'selected' : '' }}>Năm nay</option>
                <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Tùy chỉnh khoảng ngày...</option>
            </select>

            <div id="customDateWrap" style="display: {{ $filter == 'custom' ? 'flex' : 'none' }}; gap:10px; align-items:center;">
                <input type="date" name="from" value="{{ $from ?? $startDate->format('Y-m-d') }}" class="form-control" style="width:140px; padding:0.4rem;">
                <span style="color:var(--muted); font-size:0.9rem;">đến</span>
                <input type="date" name="to" value="{{ $to ?? $endDate->format('Y-m-d') }}" class="form-control" style="width:140px; padding:0.4rem;">
                <button type="submit" class="btn btn-primary" style="padding:0.4rem 0.8rem;"><i class="fa-solid fa-magnifying-glass"></i> Lọc</button>
            </div>
        </form>

        <!-- Dropdown Xuất Báo Cáo -->
        <div style="position:relative;">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('exportMenu').classList.toggle('show')" style="gap:5px;">
                <i class="fa-solid fa-download"></i> Xuất Báo Cáo <i class="fa-solid fa-caret-down"></i>
            </button>
            <div id="exportMenu" class="dropdown-menu" style="display:none; position:absolute; right:0; top:110%; background:#fff; border:1px solid var(--border); border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.15); z-index:1050; min-width:220px;">
                <form action="{{ route('admin.reports.export-orders') }}" method="GET" style="margin:0;">
                    <input type="hidden" name="from" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="to" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" style="width:100%; text-align:left; background:none; border:none; padding:10px 15px; cursor:pointer; font-family:inherit; font-size:14px;"><i class="fa-solid fa-file-excel" style="color:#10b981; width:20px;"></i> Xuất Đơn hàng (CSV)</button>
                </form>
                <form action="{{ route('admin.reports.export-revenue') }}" method="GET" style="margin:0; border-top:1px solid var(--border);">
                    <input type="hidden" name="from" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="to" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" style="width:100%; text-align:left; background:none; border:none; padding:10px 15px; cursor:pointer; font-family:inherit; font-size:14px;"><i class="fa-solid fa-file-invoice-dollar" style="color:#f59e0b; width:20px;"></i> Xuất Doanh thu (CSV)</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <p style="color:var(--muted); margin-bottom: 20px;">Số liệu từ <strong>{{ $startDate->format('d/m/Y') }}</strong> đến <strong>{{ $endDate->format('d/m/Y') }}</strong></p>

    <!-- KPI CARDS -->
    <div class="stats" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--primary);"><i class="fa-solid fa-wallet"></i></div>
            <div class="label">Tổng Doanh Thu</div>
            <div class="value" style="color:var(--primary); font-size:1.3rem;">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#10b981;"><i class="fa-solid fa-box-open"></i></div>
            <div class="label">Đơn Hàng Mới</div>
            <div class="value" style="color:#10b981; font-size:1.3rem;">{{ number_format($totalCompletedOrders) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f59e0b;"><i class="fa-solid fa-receipt"></i></div>
            <div class="label">AOV (Giá trị TB đơn)</div>
            <div class="value" style="color:#f59e0b; font-size:1.3rem;">{{ number_format($aov, 0, ',', '.') }} ₫</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ef4444;"><i class="fa-solid fa-rotate-left"></i></div>
            <div class="label">Tỷ Lệ Hủy/Hoàn</div>
            <div class="value" style="color:#ef4444; font-size:1.3rem;">{{ number_format($returnCancelRate, 1) }}%</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#8b5cf6;"><i class="fa-solid fa-user-plus"></i></div>
            <div class="label">Khách Hàng Mới</div>
            <div class="value" style="color:#8b5cf6; font-size:1.3rem;">{{ number_format($newCustomers) }}</div>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="grid-2" style="margin-bottom: 1.5rem;">
        <div class="card">
            <div class="card-head">
                <div class="card-title">Biểu đồ Doanh Thu & Đơn Hàng</div>
            </div>
            <div style="height: 300px; position:relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-head">
                <div class="card-title">Doanh Thu Theo Thương Hiệu</div>
            </div>
            <div style="height: 300px; position:relative; display:flex; justify-content:center; align-items:center;">
                <canvas id="brandPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- TABLES: PRODUCTS & CUSTOMERS -->
    <div class="grid-2" style="margin-bottom: 1.5rem;">
        <!-- Top Spenders -->
        <div class="card flush">
            <div class="card-head" style="padding:15px 20px;">
                <div class="card-title"><i class="fa-solid fa-crown" style="color:#f59e0b; margin-right:5px;"></i> Top Khách Hàng VIP</div>
            </div>
            <div class="table-wrap">
                <table class="data" style="margin:0;">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th style="text-align:center;">Số Đơn</th>
                            <th style="text-align:right;">Đã Chi Tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topSpenders as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong><br>
                                <span style="font-size:0.75rem; color:var(--muted)">{{ $user->email }}</span>
                            </td>
                            <td style="text-align:center;">{{ $user->total_orders }}</td>
                            <td style="text-align:right; color:var(--primary); font-weight:bold;">{{ number_format($user->total_spent, 0, ',', '.') }} ₫</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:20px; color:var(--muted);">Chưa có dữ liệu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Best Sellers -->
        <div class="card flush">
            <div class="card-head" style="padding:15px 20px;">
                <div class="card-title"><i class="fa-solid fa-fire" style="color:#ef4444; margin-right:5px;"></i> Sản Phẩm Bán Chạy Nhất</div>
            </div>
            <div class="table-wrap">
                <table class="data" style="margin:0;">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align:center;">Đã Bán</th>
                            <th style="text-align:right;">Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bestSellers as $product)
                        <tr>
                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <strong>{{ $product->product_name }}</strong>
                            </td>
                            <td style="text-align:center;"><span class="badge" style="background:#dcfce7; color:#166534;">{{ $product->sold }}</span></td>
                            <td style="text-align:right; font-weight:bold;">{{ number_format($product->revenue, 0, ',', '.') }} ₫</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:20px; color:var(--muted);">Chưa có dữ liệu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DEAD STOCK AND WARNINGS -->
    <div class="grid-2">
        <div class="card flush">
            <div class="card-head" style="padding:15px 20px;">
                <div class="card-title"><i class="fa-solid fa-triangle-exclamation" style="color:#ef4444; margin-right:5px;"></i> Cảnh Báo Sắp Hết Hàng</div>
            </div>
            <div class="table-wrap">
                <table class="data" style="margin:0;">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align:center;">Tồn Kho</th>
                            <th style="text-align:right;">Giá Bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockWarning as $product)
                        <tr>
                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $product->name }}
                            </td>
                            <td style="text-align:center;"><span class="badge" style="background:#fee2e2; color:#991b1b;">{{ $product->stock }}</span></td>
                            <td style="text-align:right;">{{ number_format($product->price, 0, ',', '.') }} ₫</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:20px; color:var(--muted);">Không có sản phẩm nào sắp hết hàng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card flush">
            <div class="card-head" style="padding:15px 20px;">
                <div class="card-title"><i class="fa-solid fa-snowflake" style="color:#3b82f6; margin-right:5px;"></i> Dead Stock (Tồn kho > 90 ngày)</div>
            </div>
            <div class="table-wrap">
                <table class="data" style="margin:0;">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align:center;">Tồn Kho</th>
                            <th style="text-align:right;">Giá Trị Tồn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deadStock as $product)
                        <tr>
                            <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $product->name }}
                            </td>
                            <td style="text-align:center;">{{ $product->stock }}</td>
                            <td style="text-align:right; color:var(--muted);">{{ number_format($product->price * $product->stock, 0, ',', '.') }} ₫</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center; padding:20px; color:var(--muted);">Quản lý kho đang rất tốt, không có hàng tồn đọng.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleCustomDates(val) {
        if(val === 'custom') {
            document.getElementById('customDateWrap').style.display = 'flex';
        } else {
            document.getElementById('customDateWrap').style.display = 'none';
            document.getElementById('filterForm').submit();
        }
    }

    // Dropdown Logic
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('exportMenu');
        if(!e.target.closest('#exportMenu') && !e.target.closest('.btn-primary')) {
            menu.classList.remove('show');
            menu.style.display = 'none';
        } else if (e.target.closest('.btn-primary')) {
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }
    });

    // Charts
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartValues = {!! json_encode($chartValues) !!};
    
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: chartValues,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4f46e5',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    const pieLabels = {!! json_encode($pieLabels) !!};
    const pieValues = {!! json_encode($pieValues) !!};

    if(pieLabels.length > 0) {
        new Chart(document.getElementById('brandPieChart'), {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieValues,
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#0ea5e9']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    } else {
        document.getElementById('brandPieChart').parentElement.innerHTML = '<div style="color:var(--muted)">Chưa có dữ liệu</div>';
    }
</script>
@endsection
