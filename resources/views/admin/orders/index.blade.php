@extends('layouts.admin')

@section('title', 'Đơn hàng')
@section('subtitle', 'Theo dõi & cập nhật trạng thái vận chuyển')

@section('actions')
<a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-sm">
    <i class="fa-solid fa-hourglass-half"></i> Chờ xác nhận ({{ $counts['pending'] }})
</a>
@endsection

@section('content')
<div class="status-tabs">
    @foreach([
        ''           => 'Tất cả',
        'pending'    => 'Chờ xác nhận',
        'preparing'  => 'Chuẩn bị hàng',
        'picked_up'  => 'Đã lấy hàng',
        'delivering' => 'Đang giao',
        'completed'  => 'Giao thành công',
        'finished'   => 'Đã hoàn thành',
        'returning'  => 'Đổi trả',
        'cancelled'  => 'Đã hủy',
    ] as $key => $label)
        @php $countKey = $key === '' ? 'all' : $key; @endphp
        <a href="{{ route('admin.orders.index', array_filter(['status' => $key ?: null, 'payment_status' => request('payment_status'), 'search' => request('search'), 'sort' => request('sort')])) }}"
           class="{{ request('status', '') === $key ? 'is-active' : '' }}">
            {{ $label }} · {{ $counts[$countKey] ?? 0 }}
        </a>
    @endforeach
</div>

<form class="filters" method="GET">
    <div style="flex:1;min-width:200px;">
        <label>Tìm đơn / SĐT / mã GHN</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="VD: 12 hoặc 09..." style="width:100%;">
    </div>
    <div style="min-width:180px;">
        <label>Trạng thái</label>
        <select name="status">
            <option value="">Tất cả</option>
            @foreach([
                'pending'    => 'Chờ xác nhận',
                'preparing'  => 'Chuẩn bị hàng',
                'picked_up'  => 'Đã lấy hàng',
                'delivering' => 'Đang giao',
                'completed'  => 'Giao thành công',
                'finished'   => 'Đã hoàn thành',
                'returning'  => 'Đổi trả',
                'cancelled'  => 'Đã hủy',
            ] as $k => $v)
                <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div style="min-width:150px;">
        <label>Thanh toán</label>
        <select name="payment_status">
            <option value="">Tất cả</option>
            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã TT</option>
            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Chưa TT</option>
        </select>
    </div>
    <div style="min-width:200px;">
        <label>Sắp xếp theo ngày</label>
        <select name="sort">
            <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất → cũ nhất</option>
            <option value="oldest" {{ ($sort ?? '') === 'oldest' ? 'selected' : '' }}>Cũ nhất → mới nhất</option>
        </select>
    </div>
    <button class="btn" type="submit"><i class="fa-solid fa-arrow-down-wide-short"></i> Áp dụng</button>
</form>

@if(request()->hasAny(['date', 'cod_unpaid', 'shipping_delayed', 'payment_status', 'payment_method', 'status', 'search']))
<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
    <span style="font-size:0.85rem;color:var(--muted);"><i class="fa-solid fa-filter"></i> Đang lọc:</span>
    @if(request('date') === 'today')
        <span class="badge" style="background:var(--teal-soft);color:var(--teal);">Đơn tạo hôm nay <a href="{{ route('admin.orders.index', request()->except('date')) }}" style="margin-left:4px;color:inherit;font-weight:bold;">&times;</a></span>
    @endif
    @if(request('cod_unpaid'))
        <span class="badge" style="background:var(--warn-soft);color:var(--warn);">Tiền COD chưa thu <a href="{{ route('admin.orders.index', request()->except('cod_unpaid')) }}" style="margin-left:4px;color:inherit;font-weight:bold;">&times;</a></span>
    @endif
    @if(request('shipping_delayed'))
        <span class="badge" style="background:var(--danger-soft);color:var(--danger);">Đơn giao chậm (> 3 ngày) <a href="{{ route('admin.orders.index', request()->except('shipping_delayed')) }}" style="margin-left:4px;color:inherit;font-weight:bold;">&times;</a></span>
    @endif
    @if(request('status'))
        <span class="badge" style="background:var(--ok-soft);color:var(--ok);">Trạng thái: {{ request('status') === 'completed' ? 'Đã hoàn tất / Giao thành công' : request('status') }} <a href="{{ route('admin.orders.index', request()->except('status')) }}" style="margin-left:4px;color:inherit;font-weight:bold;">&times;</a></span>
    @endif
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline" style="padding:2px 8px;font-size:0.75rem;">Xóa bộ lọc</a>
</div>
@endif

<div class="card flush" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách</th>
                    <th>Sản phẩm</th>
                    <th>Tổng</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>GHN</th>
                    <th>Ngày</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><strong>#{{ $order->order_code ?? $order->id }}</strong></td>
                    <td>
                        {{ $order->recipient_name }}<br>
                        <span class="muted">{{ $order->recipient_phone }}</span>
                    </td>
                    <td>{{ $order->items->sum('quantity') }}</td>
                    <td><strong>{{ number_format($order->total, 0, ',', '.') }} ₫</strong></td>
                    <td>
                        <div><span class="badge" style="background:var(--surface-soft);color:var(--ink-soft)">{{ strtoupper($order->payment_method) }}</span></div>
                        <div style="margin-top: 4px;">
                            @if($order->payment_status === 'paid')
                                <span class="badge" style="background:#16a34a18;color:#16a34a"><i class="fa-solid fa-check"></i> Đã TT</span>
                            @else
                                <span class="badge" style="background:#dc262618;color:#dc2626"><i class="fa-solid fa-clock"></i> Chưa TT</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background:{{ $order->status_color }}18;color:{{ $order->status_color }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="muted">{{ $order->ghn_order_code ?: '—' }}</td>
                    <td class="muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td><a class="btn btn-sm btn-outline" href="{{ route('admin.orders.show', $order) }}">Chi tiết</a></td>
                </tr>
            @empty
                <tr><td colspan="9"><div class="empty-state"><i class="fa-solid fa-inbox"></i>Chưa có đơn hàng.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links('vendor.pagination.admin') }}</div>
@endsection
