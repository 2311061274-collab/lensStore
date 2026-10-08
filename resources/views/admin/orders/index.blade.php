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

<div class="card flush" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách</th>
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
                    <td><strong>#{{ $order->id }}</strong></td>
                    <td>
                        {{ $order->recipient_name }}<br>
                        <span class="muted">{{ $order->recipient_phone }}</span>
                    </td>
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
                <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-inbox"></i>Chưa có đơn hàng.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links('vendor.pagination.admin') }}</div>
@endsection
