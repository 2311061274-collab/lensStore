@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Tổng quan vận hành & tài chính LensStore · Lần cập nhật cuối: ' . $lastUpdated)

@section('actions')
<div style="display:flex;align-items:center;gap:10px;">
    <button type="button" onclick="syncDashboardData(false);" class="btn btn-outline btn-sm" id="btnManualRefresh" title="Làm mới số liệu ngay lập tức">
        <i class="fa-solid fa-rotate-right" id="refreshIcon"></i> <span id="refreshBtnText">Làm mới</span>
    </button>
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-bell"></i> <span data-stat="pendingOrders">{{ $pendingOrders }}</span> chờ duyệt
    </a>
</div>
@endsection

@section('content')
<style>
.stat-card-link {
    text-decoration: none !important;
    color: inherit !important;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    border: 1px solid var(--line);
}
.stat-card-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px -4px rgba(15,23,42,.12);
    border-color: var(--primary, #4f46e5);
}
.stat-card-link:focus-visible {
    outline: 2px solid var(--primary, #4f46e5);
    outline-offset: 2px;
}
.badge-out-of-stock {
    background: #fee2e2;
    color: #991b1b;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}
.badge-low-stock {
    background: #ffedd5;
    color: #c2410c;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}
.badge-delayed {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 0.78rem;
    font-weight: 600;
}
</style>

{{-- Hàng thẻ thống kê 1: Đơn hôm nay, Chờ duyệt, Hoàn tất, Doanh thu hôm nay --}}
<div class="stats" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
    <a href="{{ route('admin.orders.index', ['date' => 'today']) }}" class="stat-card stat-card-link" title="Nhấp để xem danh sách đơn hàng tạo hôm nay">
        <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
        <div class="label">Đơn hôm nay <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value" data-stat="ordersToday">{{ $ordersToday }}</div>
        <div class="hint">Tuần: <span data-stat="ordersWeek">{{ $ordersWeek }}</span> · Tháng: <span data-stat="ordersMonth">{{ $ordersMonth }}</span></div>
    </a>
    
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="stat-card stat-card-link tone-warn" title="Nhấp để xem danh sách đơn hàng chờ xác nhận">
        <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
        <div class="label">Chờ xác nhận <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value text-warn" data-stat="pendingOrders">{{ $pendingOrders }}</div>
        <div class="hint">Chuẩn bị: <span data-stat="preparingOrders">{{ $preparingOrders }}</span> · Giao: <span data-stat="shippingOrders">{{ $shippingOrders }}</span></div>
    </a>

    <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="stat-card stat-card-link tone-ok" title="Nhấp để xem danh sách đơn hàng đã hoàn tất / giao thành công">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="label">Đơn hoàn tất <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value" style="color:var(--ok)" data-stat="completedOrders">{{ $completedOrders }}</div>
        <div class="hint">Đã hủy: <span data-stat="cancelledOrders">{{ $cancelledOrders }}</span> đơn</div>
    </a>

    @if($isAdmin)
    <a href="{{ route('admin.reports.index', ['filter' => 'today']) }}" class="stat-card stat-card-link tone-teal" title="Nhấp để xem báo cáo doanh thu chi tiết hôm nay">
        <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
        <div class="label">Doanh thu thực hôm nay <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value" data-stat="revenueTodayFormatted">{{ number_format($revenueToday, 0, ',', '.') }} ₫</div>
        <div class="hint">Tuần: <span data-stat="revenueWeekFormatted">{{ number_format($revenueWeek, 0, ',', '.') }} ₫</span> (chỉ tính đã thanh toán)</div>
    </a>
    @endif
</div>

{{-- Hàng thẻ thống kê 2: Doanh thu tháng, Tiền COD chưa thu, Đổi/trả chờ duyệt, Chờ hoàn tiền --}}
<div class="stats" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-top: 1rem;">
    @if($isAdmin)
    <a href="{{ route('admin.reports.index', ['filter' => 'this_month']) }}" class="stat-card stat-card-link tone-teal" title="Nhấp để xem báo cáo doanh thu tháng này">
        <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
        <div class="label">Doanh thu thực tháng <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value" data-stat="revenueMonthFormatted">{{ number_format($revenueMonth, 0, ',', '.') }} ₫</div>
        <div class="hint">Khách mới 7 ngày: <span data-stat="newCustomers">{{ $newCustomers }}</span></div>
    </a>

    <a href="{{ route('admin.orders.index', ['cod_unpaid' => 1]) }}" class="stat-card stat-card-link tone-warn" title="Nhấp để xem danh sách đơn hàng COD còn phải thu tiền">
        <div class="stat-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <div class="label">Tiền COD chưa thu <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value text-warn" data-stat="pendingCodAmountFormatted">{{ number_format($pendingCodAmount, 0, ',', '.') }} ₫</div>
        <div class="hint">Đơn COD đang vận chuyển (chưa thực thu)</div>
    </a>
    @endif

    <a href="{{ route('admin.returns.index', ['status' => 'pending']) }}" class="stat-card stat-card-link {{ $pendingReturns > 0 ? 'tone-danger' : '' }}" title="Nhấp để xem các yêu cầu đổi trả đang chờ xử lý">
        <div class="stat-icon"><i class="fa-solid fa-rotate-left"></i></div>
        <div class="label">Đổi/trả chờ duyệt <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value {{ $pendingReturns > 0 ? 'text-danger' : '' }}" data-stat="pendingReturns">{{ $pendingReturns }}</div>
        <div class="hint">Xem danh sách yêu cầu đổi trả &rarr;</div>
    </a>

    <a href="{{ route('admin.returns.index', ['refund_status' => 'pending']) }}" class="stat-card stat-card-link {{ $pendingRefunds > 0 ? 'tone-warn' : 'tone-ok' }}" title="Nhấp để xem danh sách yêu cầu chờ chuyển khoản hoàn tiền">
        <div class="stat-icon"><i class="fa-solid fa-money-bill-transfer"></i></div>
        <div class="label">Chờ hoàn tiền COD/online <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;opacity:0.6;"></i></div>
        <div class="value {{ $pendingRefunds > 0 ? 'text-warn' : '' }}" data-stat="pendingRefunds">{{ $pendingRefunds }}</div>
        <div class="hint">Đã hoàn: <span data-stat="totalRefundedFormatted">{{ number_format($totalRefunded, 0, ',', '.') }} ₫</span></div>
    </a>
</div>

{{-- Biểu đồ --}}
@if($isAdmin)
<div class="grid-2">
    <div class="card">
        <div class="card-head">
            <div class="card-title"><i class="fa-solid fa-chart-line text-teal"></i> Xu hướng doanh thu 14 ngày</div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline">Xem báo cáo &rarr;</a>
        </div>
        <div class="chart-box" style="position:relative;height:280px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-head">
            <div class="card-title"><i class="fa-solid fa-chart-pie text-ok"></i> Tỷ lệ bán theo thương hiệu</div>
            <span class="badge" style="background:var(--ok-soft);color:var(--ok)">Đã giao thành công</span>
        </div>
        <div id="brandChartEmptyState" class="empty-state" style="{{ empty($brandPie) ? '' : 'display:none;' }}">
            <i class="fa-solid fa-chart-pie"></i>
            Chưa có sản phẩm nào từ đơn giao thành công.
        </div>
        <div class="chart-box" id="brandChartBox" style="position:relative;height:280px;{{ empty($brandPie) ? 'display:none;' : '' }}">
            <canvas id="brandChart"></canvas>
        </div>
    </div>
</div>
@endif

{{-- Danh sách Cảnh báo tồn kho & Đơn giao chậm --}}
<div class="grid-2">
    {{-- Cảnh báo kho: Hết hàng (stock <= 0) & Sắp hết (0 < stock < 3) --}}
    <div class="card">
        <div class="card-head">
            <div class="card-title">
                <i class="fa-solid fa-triangle-exclamation text-warn"></i> Cảnh báo kho
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <span class="badge badge-out-of-stock" id="badgeOutOfStock" title="Sản phẩm tồn kho = 0">Hết: {{ $outOfStockCount }}</span>
                <span class="badge badge-low-stock" id="badgeLowStock" title="Sản phẩm tồn kho < 3">Sắp hết: {{ $lowStockCount }}</span>
                <a href="{{ route('admin.products.index', ['stock_status' => 'alert']) }}" class="btn btn-sm btn-outline" title="Mở trang quản lý sản phẩm với bộ lọc cảnh báo kho">Xem tất cả &rarr;</a>
            </div>
        </div>
        
        <div id="stockAlertsEmptyState" class="empty-state" style="{{ $lowStock->isEmpty() ? '' : 'display:none;' }}">
            <i class="fa-solid fa-box-open"></i>
            Không có sản phẩm nào hết hàng hoặc sắp hết.
        </div>

        <div class="table-wrap" id="stockAlertsTableWrap" style="{{ $lowStock->isEmpty() ? 'display:none;' : '' }}">
            <table class="data">
                <thead>
                    <tr>
                        <th width="45">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Danh mục</th>
                        <th style="text-align:center;">Tồn</th>
                        <th style="text-align:center;">Trạng thái</th>
                        <th style="text-align:right;"></th>
                    </tr>
                </thead>
                <tbody id="stockAlertsTableBody">
                @foreach($lowStock as $p)
                    <tr>
                        <td>
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" width="38" height="38" style="object-fit:cover;border-radius:6px;border:1px solid var(--line);">
                        </td>
                        <td>
                            <strong><a href="{{ route('admin.products.show', $p) }}" style="color:inherit;">{{ $p->name }}</a></strong>
                            <div class="muted" style="font-size:0.78rem;">SKU: {{ $p->sku ?: '—' }} · {{ $p->brand ?: '—' }}</div>
                        </td>
                        <td class="muted" style="font-size:0.85rem;">{{ $p->category->name ?? '—' }}</td>
                        <td style="text-align:center;">
                            <strong class="{{ $p->stock <= 0 ? 'text-danger' : 'text-warn' }}">{{ $p->stock }}</strong>
                        </td>
                        <td style="text-align:center;">
                            @if($p->stock <= 0)
                                <span class="badge-out-of-stock"><i class="fa-solid fa-circle-xmark"></i> Hết hàng</span>
                            @else
                                <span class="badge-low-stock"><i class="fa-solid fa-triangle-exclamation"></i> Sắp hết</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <a class="btn btn-sm btn-outline" href="{{ route('admin.products.edit', $p) }}" title="Chỉnh sửa sản phẩm"><i class="fa-solid fa-pen"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Đơn giao chậm: Đang vận chuyển > 3 ngày --}}
    <div class="card">
        <div class="card-head">
            <div class="card-title">
                <i class="fa-solid fa-truck text-warn"></i> Đơn giao chậm
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <span class="badge badge-delayed" id="badgeStuckShipping">{{ $stuckShippingCount }} đơn trễ</span>
                <a href="{{ route('admin.orders.index', ['shipping_delayed' => 1]) }}" class="btn btn-sm btn-outline" title="Xem tất cả đơn giao chậm">Xem tất cả &rarr;</a>
            </div>
        </div>

        <div id="stuckShippingEmptyState" class="empty-state" style="{{ $stuckShipping->isEmpty() ? '' : 'display:none;' }}">
            <i class="fa-solid fa-truck-fast"></i>
            Không có đơn đang nghẽn giao hàng.
        </div>

        <div class="table-wrap" id="stuckShippingTableWrap" style="{{ $stuckShipping->isEmpty() ? 'display:none;' : '' }}">
            <table class="data">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Độ trễ</th>
                        <th style="text-align:right;"></th>
                    </tr>
                </thead>
                <tbody id="stuckShippingTableBody">
                @foreach($stuckShipping as $o)
                    @php
                        $daysInTransit = (int) ($o->updated_at ? $o->updated_at->diffInDays(now()) : 0);
                        $daysDelayed   = max(1, $daysInTransit - 3);
                    @endphp
                    <tr>
                        <td>
                            <strong><a href="{{ route('admin.orders.show', $o) }}" style="color:inherit;">{{ $o->order_code ?: ('#' . $o->id) }}</a></strong>
                            <div class="muted" style="font-size:0.75rem;">GHN: {{ $o->ghn_order_code ?: '—' }}</div>
                        </td>
                        <td>
                            <div>{{ $o->recipient_name }}</div>
                            <div class="muted" style="font-size:0.78rem;">{{ $o->recipient_phone }}</div>
                        </td>
                        <td class="muted" style="font-size:0.82rem;">{{ $o->created_at ? $o->created_at->format('d/m/Y') : '—' }}</td>
                        <td>
                            <span class="badge" style="background:#e0e7ff;color:#4338ca;font-size:0.75rem;">{{ $o->status_label }}</span>
                        </td>
                        <td>
                            <span class="badge-delayed">Trễ {{ $daysDelayed }} ngày (giao {{ $daysInTransit }} ngày)</span>
                        </td>
                        <td style="text-align:right;">
                            <a class="btn btn-sm btn-outline" href="{{ route('admin.orders.show', $o) }}">Xem</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Biến toàn cục quản lý dữ liệu và Chart instances
let revenueChart = null;
let brandChart = null;
let isSyncing = false;
let syncAbortController = null;
const dashboardDataUrl = @json(route('admin.dashboard.data'));
const isAdmin = @json($isAdmin);

const revenueLabels = @json($labels);
const revenueValues = @json($values);
const brandData = @json($brandPie);
const gridColor = 'rgba(20,24,31,.06)';

// Khởi tạo Biểu đồ Doanh thu
if (isAdmin && document.getElementById('revenueChart')) {
    revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenueValues,
                borderColor: '#c45c26',
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 280);
                    g.addColorStop(0, 'rgba(196,92,38,.22)');
                    g.addColorStop(1, 'rgba(196,92,38,0)');
                    return g;
                },
                fill: true,
                tension: .4,
                borderWidth: 2.5,
                pointRadius: 3,
                pointBackgroundColor: '#c45c26',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#7a8494', font: { family: 'Instrument Sans', size: 11 } } },
                y: {
                    grid: { color: gridColor },
                    ticks: {
                        color: '#7a8494',
                        font: { family: 'Instrument Sans', size: 11 },
                        callback: v => new Intl.NumberFormat('vi-VN', { notation: 'compact' }).format(v)
                    },
                    border: { display: false }
                }
            }
        }
    });
}

// Khởi tạo Biểu đồ Thương hiệu
const innerLabelsPlugin = {
    id: 'innerLabels',
    afterDatasetsDraw(chart, args, options) {
        const { ctx, data } = chart;
        const dataset = data.datasets[0];
        const meta = chart.getDatasetMeta(0);
        if (!dataset || !meta || !meta.data) return;
        const total = dataset.data.reduce((a, b) => a + b, 0);

        ctx.save();
        ctx.font = 'bold 13px "Instrument Sans", sans-serif';
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        meta.data.forEach((arc, index) => {
            const value = dataset.data[index];
            if (value > 0) {
                const pct = Math.round((value / total) * 100);
                if (pct > 5) {
                    const centerPoint = arc.tooltipPosition();
                    ctx.fillText(pct + '%', centerPoint.x, centerPoint.y);
                }
            }
        });
        ctx.restore();
    }
};

if (isAdmin && document.getElementById('brandChart')) {
    brandChart = new Chart(document.getElementById('brandChart'), {
        type: 'doughnut',
        data: {
            labels: brandData.map(b => b.brand),
            datasets: [{
                data: brandData.map(b => b.qty),
                backgroundColor: ['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true,
                        padding: 16,
                        font: { family: 'Instrument Sans', size: 13 },
                        generateLabels(chart) {
                            const data = chart.data;
                            const total = (data.datasets[0].data || []).reduce((a, b) => a + b, 0) || 1;
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0].data[i] || 0;
                                const pct = Math.round((value / total) * 100);
                                return {
                                    text: `${label} · ${value} sp (${pct}%)`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    hidden: false,
                                    index: i,
                                };
                            });
                        }
                    }
                }
            }
        },
        plugins: [innerLabelsPlugin]
    });
}

/**
 * Hàm cập nhật toàn bộ dữ liệu Dashboard thời gian thực mà KHÔNG cần F5 trang
 */
async function syncDashboardData(silent = true) {
    if (isSyncing) return;
    isSyncing = true;

    const refreshIcon = document.getElementById('refreshIcon');
    const refreshBtnText = document.getElementById('refreshBtnText');
    if (!silent && refreshIcon) {
        refreshIcon.classList.add('fa-spin');
        if (refreshBtnText) refreshBtnText.textContent = 'Đang đồng bộ...';
    }

    try {
        if (syncAbortController) {
            syncAbortController.abort();
        }
        syncAbortController = new AbortController();

        const res = await fetch(dashboardDataUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: syncAbortController.signal
        });

        if (!res.ok) throw new Error('Không thể tải dữ liệu dashboard: ' + res.status);
        const data = await res.json();

        // 1. Cập nhật các con số thống kê (Stats Cards)
        for (const [key, val] of Object.entries(data.counts)) {
            const els = document.querySelectorAll(`[data-stat="${key}"]`);
            els.forEach(el => { el.textContent = val; });
        }

        // Cập nhật các con số đã format tiền tệ
        for (const [key, val] of Object.entries(data.formatted)) {
            const els = document.querySelectorAll(`[data-stat="${key}"]`);
            els.forEach(el => { el.textContent = val; });
            const directEls = document.querySelectorAll(`[data-stat="${key}Formatted"]`);
            directEls.forEach(el => { el.textContent = val; });
        }

        // Cập nhật thời điểm
        const subtitleEl = document.querySelector('.page-header .subtitle') || document.querySelector('[data-role="subtitle"]');
        if (subtitleEl && data.lastUpdated) {
            subtitleEl.innerHTML = `Tổng quan vận hành & tài chính LensStore · Lần cập nhật cuối: ${data.lastUpdated}`;
        }

        // Cập nhật badges tồn kho
        const badgeOutOfStock = document.getElementById('badgeOutOfStock');
        if (badgeOutOfStock) badgeOutOfStock.textContent = `Hết: ${data.counts.outOfStockCount}`;
        const badgeLowStock = document.getElementById('badgeLowStock');
        if (badgeLowStock) badgeLowStock.textContent = `Sắp hết: ${data.counts.lowStockCount}`;

        // Cập nhật badge đơn giao chậm
        const badgeStuck = document.getElementById('badgeStuckShipping');
        if (badgeStuck) badgeStuck.textContent = `${data.counts.stuckShippingCount} đơn trễ`;

        // 2. Cập nhật Bảng Cảnh báo tồn kho (Hết hàng & Sắp hết)
        const stockBody = document.getElementById('stockAlertsTableBody');
        const stockEmpty = document.getElementById('stockAlertsEmptyState');
        const stockWrap = document.getElementById('stockAlertsTableWrap');
        if (stockBody && data.lowStock) {
            if (data.lowStock.length === 0) {
                if (stockEmpty) stockEmpty.style.display = '';
                if (stockWrap) stockWrap.style.display = 'none';
            } else {
                if (stockEmpty) stockEmpty.style.display = 'none';
                if (stockWrap) stockWrap.style.display = '';
                stockBody.innerHTML = data.lowStock.map(p => `
                    <tr>
                        <td>
                            <img src="${p.image_url}" alt="${p.name}" width="38" height="38" style="object-fit:cover;border-radius:6px;border:1px solid var(--line);">
                        </td>
                        <td>
                            <strong><a href="${p.show_url}" style="color:inherit;">${p.name}</a></strong>
                            <div class="muted" style="font-size:0.78rem;">SKU: ${p.sku} · ${p.brand}</div>
                        </td>
                        <td class="muted" style="font-size:0.85rem;">${p.category_name}</td>
                        <td style="text-align:center;">
                            <strong class="${p.stock <= 0 ? 'text-danger' : 'text-warn'}">${p.stock}</strong>
                        </td>
                        <td style="text-align:center;">
                            ${p.is_out_of_stock 
                                ? `<span class="badge-out-of-stock"><i class="fa-solid fa-circle-xmark"></i> Hết hàng</span>` 
                                : `<span class="badge-low-stock"><i class="fa-solid fa-triangle-exclamation"></i> Sắp hết</span>`}
                        </td>
                        <td style="text-align:right;">
                            <a class="btn btn-sm btn-outline" href="${p.edit_url}" title="Chỉnh sửa sản phẩm"><i class="fa-solid fa-pen"></i></a>
                        </td>
                    </tr>
                `).join('');
            }
        }

        // 3. Cập nhật Bảng Đơn giao chậm
        const shippingBody = document.getElementById('stuckShippingTableBody');
        const shippingEmpty = document.getElementById('stuckShippingEmptyState');
        const shippingWrap = document.getElementById('stuckShippingTableWrap');
        if (shippingBody && data.stuckShipping) {
            if (data.stuckShipping.length === 0) {
                if (shippingEmpty) shippingEmpty.style.display = '';
                if (shippingWrap) shippingWrap.style.display = 'none';
            } else {
                if (shippingEmpty) shippingEmpty.style.display = 'none';
                if (shippingWrap) shippingWrap.style.display = '';
                shippingBody.innerHTML = data.stuckShipping.map(o => `
                    <tr>
                        <td>
                            <strong><a href="${o.show_url}" style="color:inherit;">${o.order_code}</a></strong>
                            <div class="muted" style="font-size:0.75rem;">GHN: ${o.ghn_order_code}</div>
                        </td>
                        <td>
                            <div>${o.recipient_name}</div>
                            <div class="muted" style="font-size:0.78rem;">${o.recipient_phone}</div>
                        </td>
                        <td class="muted" style="font-size:0.82rem;">${o.created_at_formatted}</td>
                        <td>
                            <span class="badge" style="background:#e0e7ff;color:#4338ca;font-size:0.75rem;">${o.status_label}</span>
                        </td>
                        <td>
                            <span class="badge-delayed">Trễ ${o.days_delayed} ngày (giao ${o.days_in_transit} ngày)</span>
                        </td>
                        <td style="text-align:right;">
                            <a class="btn btn-sm btn-outline" href="${o.show_url}">Xem</a>
                        </td>
                    </tr>
                `).join('');
            }
        }

        // 4. Cập nhật Biểu đồ Doanh thu (Chart.js update in-place)
        if (revenueChart && data.charts && data.charts.labels) {
            revenueChart.data.labels = data.charts.labels;
            revenueChart.data.datasets[0].data = data.charts.values;
            revenueChart.update('none'); // Cập nhật mượt mà không nhấp nháy
        }

        // 5. Cập nhật Biểu đồ Thương hiệu
        if (brandChart && data.charts && data.charts.brandPie) {
            const bp = data.charts.brandPie;
            const brandEmpty = document.getElementById('brandChartEmptyState');
            const brandBox = document.getElementById('brandChartBox');
            if (bp.length === 0) {
                if (brandEmpty) brandEmpty.style.display = '';
                if (brandBox) brandBox.style.display = 'none';
            } else {
                if (brandEmpty) brandEmpty.style.display = 'none';
                if (brandBox) brandBox.style.display = '';
                brandChart.data.labels = bp.map(b => b.brand);
                brandChart.data.datasets[0].data = bp.map(b => b.qty);
                brandChart.update('none');
            }
        }

    } catch (err) {
        if (err.name !== 'AbortError') {
            console.warn('[Dashboard Auto-Sync]', err);
        }
    } finally {
        isSyncing = false;
        if (!silent && refreshIcon) {
            refreshIcon.classList.remove('fa-spin');
            if (refreshBtnText) refreshBtnText.textContent = 'Làm mới';
        }
    }
}

// Lắng nghe sự kiện đồng bộ giữa các tab trình duyệt (Multi-tab synchronization)
try {
    const channel = new BroadcastChannel('lensStore_channel');
    channel.onmessage = (event) => {
        if (event.data && event.data.type === 'data_changed') {
            syncDashboardData(true);
        }
    };
} catch (e) {
    // Trình duyệt không hỗ trợ BroadcastChannel
}

window.addEventListener('storage', (e) => {
    if (e.key === 'lensStore_sync_trigger') {
        syncDashboardData(true);
    }
});

// Cơ chế tự động đồng bộ chu kỳ ngầm (heartbeat 8 giây khi tab đang active)
setInterval(() => {
    if (!document.hidden && navigator.onLine) {
        syncDashboardData(true);
    }
}, 8000);

// Đồng bộ ngay khi người dùng quay lại tab hoặc có mạng trở lại
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        syncDashboardData(true);
    }
});
window.addEventListener('online', () => {
    syncDashboardData(true);
});
</script>
@endpush
