@extends('layouts.admin')

@section('title', 'Yêu cầu trả hàng')

@section('content')
<div class="topbar">
    <div>
        <div class="breadcrumb">Quản lý / Đổi Trả</div>
        <h1>Yêu cầu Trả hàng / Hoàn tiền</h1>
    </div>
</div>

<div class="content">
    @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <div class="stats" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 1.25rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#4f46e5;"><i class="fa-solid fa-boxes-packing"></i></div>
            <div class="label">Tổng yêu cầu</div>
            <div class="value">{{ $counts['all'] }}</div>
            <div class="hint">Tất cả yêu cầu đổi trả</div>
        </div>
        <div class="stat-card tone-warn">
            <div class="stat-icon" style="background:#f59e0b;"><i class="fa-solid fa-clock"></i></div>
            <div class="label">Chờ duyệt</div>
            <div class="value text-warn">{{ $counts['pending'] }}</div>
            <div class="hint">Cần xử lý phê duyệt</div>
        </div>
        <div class="stat-card tone-ok">
            <div class="stat-icon" style="background:#10b981;"><i class="fa-solid fa-circle-check"></i></div>
            <div class="label">Đã chấp nhận hoàn</div>
            <div class="value text-ok">{{ $counts['approved'] }}</div>
            <div class="hint">Chấp thuận đổi trả</div>
        </div>
        <div class="stat-card tone-danger">
            <div class="stat-icon" style="background:#ef4444;"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="label">Đã từ chối</div>
            <div class="value" style="color:var(--danger);">{{ $counts['rejected'] }}</div>
            <div class="hint">Yêu cầu không được duyệt</div>
        </div>
        <div class="stat-card tone-info">
            <div class="stat-icon" style="background:#0ea5e9;"><i class="fa-solid fa-money-bill-transfer"></i></div>
            <div class="label">Tổng tiền phải hoàn</div>
            <div class="value text-primary" style="font-size:1.4rem;">{{ number_format($totalApprovedRefund, 0, ',', '.') }} ₫</div>
            <div class="hint">Đã duyệt (+ {{ number_format($totalPendingRefund, 0, ',', '.') }} ₫ chờ)</div>
        </div>
    </div>

    @if(!empty($reasonCounts) && count($reasonCounts) > 0)
    <div class="grid-2" style="margin-bottom: 1.25rem;">
        <div class="card">
            <div class="card-head">
                <div class="card-title"><i class="fa-solid fa-chart-pie" style="color:var(--primary);margin-right:6px;"></i> Phân bố lý do đổi trả</div>
                <span class="badge" style="background:var(--surface-soft);color:var(--ink-soft)">{{ array_sum($reasonCounts) }} phản hồi</span>
            </div>
            <div style="height:220px;position:relative;">
                <canvas id="returnReasonChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-head">
                <div class="card-title"><i class="fa-solid fa-list-check" style="color:var(--teal);margin-right:6px;"></i> Chi tiết theo lý do</div>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.75rem;padding-top:0.25rem;">
                @php $totalR = max(1, array_sum($reasonCounts)); @endphp
                @foreach($reasonCounts as $rReason => $rTotal)
                @php $pct = round(($rTotal / $totalR) * 100); @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
                        <span style="font-weight:600;color:var(--ink);">{{ $rReason }}</span>
                        <span style="color:var(--muted);font-weight:600;">{{ $rTotal }} ({{ $pct }}%)</span>
                    </div>
                    <div style="width:100%;height:7px;background:var(--surface-soft);border-radius:99px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg, var(--primary), #818cf8);border-radius:99px;"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="status-tabs">
        <a href="{{ route('admin.returns.index') }}" class="{{ empty($status) ? 'is-active' : '' }}">
            Tất cả · {{ $counts['all'] }}
        </a>
        <a href="{{ route('admin.returns.index', ['status' => 'pending']) }}" class="{{ ($status ?? '') === 'pending' ? 'is-active' : '' }}">
            Chờ duyệt · {{ $counts['pending'] }}
        </a>
        <a href="{{ route('admin.returns.index', ['status' => 'approved']) }}" class="{{ ($status ?? '') === 'approved' ? 'is-active' : '' }}">
            Đã chấp nhận · {{ $counts['approved'] }}
        </a>
        <a href="{{ route('admin.returns.index', ['status' => 'rejected']) }}" class="{{ ($status ?? '') === 'rejected' ? 'is-active' : '' }}">
            Từ chối · {{ $counts['rejected'] }}
        </a>
    </div>

    <div class="card flush" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Đơn hàng</th>
                        <th style="text-align:right;">Số tiền hoàn</th>
                        <th>Lý do hoàn trả</th>
                        <th>Ghi chú</th>
                        <th>Bằng chứng</th>
                        <th>Trạng thái</th>
                        <th style="text-align:right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>
                            <strong>{{ $req->user->name ?? 'Khách vãng lai' }}</strong>
                            @if(!empty($req->user->email))
                                <div style="font-size:0.75rem;color:var(--muted);margin-top:2px;">{{ $req->user->email }}</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $req->order_id) }}" style="color:var(--primary);font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fa-solid fa-receipt"></i> #{{ str_pad($req->order_id, 6, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        <td style="text-align:right; font-weight:700; color:var(--danger);">
                            {{ number_format($req->order->total ?? 0, 0, ',', '.') }} ₫
                        </td>
                        <td>
                            <span style="font-weight:600;color:var(--ink);">{{ $req->reason }}</span>
                        </td>
                        <td style="max-width:240px;font-size:0.84rem;color:var(--ink-soft);line-height:1.4;">
                            {{ $req->note ?: '—' }}
                        </td>
                        <td>
                            @if($req->image)
                                <a href="{{ asset($req->image) }}" target="_blank" class="btn btn-sm btn-outline" style="padding:0.25rem 0.6rem;font-size:0.75rem;">
                                    <i class="fa-regular fa-image"></i> Xem ảnh
                                </a>
                            @else
                                <span style="color:var(--muted);font-size:0.8rem;">Không có</span>
                            @endif
                        </td>
                        <td>
                            @if($req->status == 'pending')
                                <span class="badge" style="background:#fef9c3;color:#854d0e;border:1px solid #fef08a;">
                                    <i class="fa-solid fa-clock" style="margin-right:4px;"></i> Chờ duyệt
                                </span>
                            @elseif($req->status == 'approved')
                                <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">
                                    <i class="fa-solid fa-circle-check" style="margin-right:4px;"></i> Đã chấp nhận
                                </span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca;">
                                    <i class="fa-solid fa-circle-xmark" style="margin-right:4px;"></i> Từ chối
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;flex-wrap:wrap;">
                                <a href="{{ route('admin.orders.show', $req->order_id) }}" class="btn btn-sm btn-outline" title="Xem chi tiết đơn hàng">
                                    <i class="fa-solid fa-eye"></i> Xem đơn
                                </a>
                                @if($req->status == 'pending')
                                <form action="{{ route('admin.returns.update-status', $req->id) }}" method="POST" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Chấp nhận hoàn tiền cho đơn hàng #{{ str_pad($req->order_id, 6, '0', STR_PAD_LEFT) }}?');">
                                        <i class="fa-solid fa-check"></i> Duyệt
                                    </button>
                                </form>
                                <form action="{{ route('admin.returns.update-status', $req->id) }}" method="POST" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Từ chối yêu cầu đổi trả cho đơn #{{ str_pad($req->order_id, 6, '0', STR_PAD_LEFT) }}?');">
                                        <i class="fa-solid fa-xmark"></i> Từ chối
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
                                Chưa có yêu cầu đổi trả nào.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top:1.25rem;">
        {{ $requests->links('vendor.pagination.admin') }}
    </div>
</div>
@endsection

@if(!empty($reasonCounts) && count($reasonCounts) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('returnReasonChart');
    if (!canvas) return;
    const labels = @json(array_keys($reasonCounts));
    const dataValues = @json(array_values($reasonCounts));
    const palette = ['#4f46e5', '#f59e0b', '#ef4444', '#10b981', '#06b6d4', '#8b5cf6', '#ec4899'];

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                backgroundColor: palette.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 12,
                        font: { size: 12, family: 'Inter' }
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endpush
@endif
