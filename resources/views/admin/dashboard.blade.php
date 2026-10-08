@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Tổng quan kinh doanh LensStore · ' . now()->format('d/m/Y'))

@section('actions')
<a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-outline btn-sm">
    <i class="fa-solid fa-bell"></i> {{ $pendingOrders }} chờ xác nhận
</a>
@endsection

@section('content')
<div class="stats">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
        <div class="label">Đơn hôm nay</div>
        <div class="value">{{ $ordersToday }}</div>
        <div class="hint">Tuần {{ $ordersWeek }} · Tháng {{ $ordersMonth }}</div>
    </div>
    <div class="stat-card tone-warn">
        <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
        <div class="label">Chờ xác nhận</div>
        <div class="value text-warn">{{ $pendingOrders }}</div>
        <div class="hint">Đang giao: {{ $shippingOrders }}</div>
    </div>
    @if($isAdmin)
    <div class="stat-card tone-teal">
        <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
        <div class="label">Doanh thu hôm nay</div>
        <div class="value">{{ number_format($revenueToday, 0, ',', '.') }} ₫</div>
        <div class="hint">Tuần: {{ number_format($revenueWeek, 0, ',', '.') }} ₫</div>
    </div>
    <div class="stat-card tone-ok">
        <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
        <div class="label">Doanh thu tháng</div>
        <div class="value">{{ number_format($revenueMonth, 0, ',', '.') }} ₫</div>
        <div class="hint">Khách mới (7 ngày): {{ $newCustomers }}</div>
    </div>
    @endif
</div>

@if($isAdmin)
<div class="grid-2">
    <div class="card">
        <div class="card-head">
            <div class="card-title">Xu hướng doanh thu 14 ngày</div>
        </div>
        <div class="chart-box">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-head">
            <div class="card-title">Tỷ lệ bán theo thương hiệu</div>
            <span class="badge" style="background:var(--ok-soft);color:var(--ok)">Đã giao thành công</span>
        </div>
        @if(empty($brandPie))
            <div class="empty-state">
                <i class="fa-solid fa-chart-pie"></i>
                Chưa có sản phẩm nào từ đơn giao thành công.
            </div>
        @else
        <div class="chart-box">
            <canvas id="brandChart"></canvas>
        </div>
        @endif
    </div>
</div>
@endif

<div class="grid-2">
    <div class="card">
        <div class="card-head">
            <div class="card-title"><i class="fa-solid fa-triangle-exclamation text-warn"></i> Sắp hết hàng</div>
            <span class="badge" style="background:var(--warn-soft);color:var(--warn)">stock &lt; 3</span>
        </div>
        @if($lowStock->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                Không có sản phẩm nào sắp hết.
            </div>
        @else
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>Sản phẩm</th><th>Brand</th><th>Tồn</th><th></th></tr></thead>
                    <tbody>
                    @foreach($lowStock as $p)
                        <tr>
                            <td><strong>{{ $p->name }}</strong></td>
                            <td class="muted">{{ $p->brand ?: '—' }}</td>
                            <td class="{{ $p->stock == 0 ? 'text-danger' : 'text-warn' }}"><strong>{{ $p->stock }}</strong></td>
                            <td><a class="btn btn-sm btn-outline" href="{{ route('admin.products.edit', $p) }}">Sửa</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="card">
        <div class="card-head">
            <div class="card-title"><i class="fa-solid fa-truck text-warn"></i> Đơn giao chậm</div>
            <span class="badge" style="background:var(--warn-soft);color:var(--warn)">&gt; 3 ngày</span>
        </div>
        @if($stuckShipping->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-truck-fast"></i>
                Không có đơn đang nghẽn giao hàng.
            </div>
        @else
            <div class="table-wrap">
                <table class="data">
                    <thead><tr><th>#</th><th>Khách</th><th>GHN</th><th></th></tr></thead>
                    <tbody>
                    @foreach($stuckShipping as $o)
                        <tr>
                            <td><strong>#{{ $o->id }}</strong></td>
                            <td>{{ $o->recipient_name }}</td>
                            <td class="muted">{{ $o->ghn_order_code ?: '—' }}</td>
                            <td><a class="btn btn-sm btn-outline" href="{{ route('admin.orders.show', $o) }}">Xem</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@if($isAdmin)
@push('scripts')
<script>
const revenueLabels = @json($labels);
const revenueValues = @json($values);
const brandData = @json($brandPie);

const gridColor = 'rgba(20,24,31,.06)';

new Chart(document.getElementById('revenueChart'), {
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

@if(!empty($brandPie))
const innerLabelsPlugin = {
    id: 'innerLabels',
    afterDatasetsDraw(chart, args, options) {
        const { ctx, data } = chart;
        const dataset = data.datasets[0];
        const meta = chart.getDatasetMeta(0);
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
                if (pct > 5) { // Only draw if big enough
                    const centerPoint = arc.tooltipPosition();
                    ctx.fillText(pct + '%', centerPoint.x, centerPoint.y);
                }
            }
        });
        ctx.restore();
    }
};

new Chart(document.getElementById('brandChart'), {
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
@endif
</script>
@endpush
@endif
