@extends('layouts.admin')

@section('title', 'Khách hàng CRM')
@section('subtitle', 'Hồ sơ 360° · Lifetime value · Phân loại VIP')

@section('content')
<form class="filters" method="GET">
    <div style="flex:1;min-width:200px;">
        <label>Tìm kiếm</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, email, SĐT" style="width:100%;">
    </div>
    <div>
        <label>Phân loại</label>
        <select name="tag">
            <option value="">Tất cả</option>
            <option value="vip" @selected(request('tag')==='vip')>VIP (≥ 20tr)</option>
            <option value="new" @selected(request('tag')==='new')>Mua 1 lần</option>
            <option value="first" @selected(request('tag')==='first')>Chưa mua</option>
        </select>
    </div>
    <button class="btn" type="submit"><i class="fa-solid fa-filter"></i> Lọc</button>
</form>

<div class="card flush" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>SĐT</th>
                    <th>Số đơn</th>
                    <th>Lifetime Value</th>
                    <th>Tag</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($customers as $c)
                @php
                    $ltv = (int) ($c->lifetime_value ?? 0);
                    $tag = $ltv >= 20000000 ? 'VIP' : ($c->orders_count == 0 ? 'Chưa mua' : ($c->orders_count == 1 ? 'Mới' : 'Thường'));
                @endphp
                <tr>
                    <td>
                        <strong>{{ $c->name }}</strong><br>
                        <span class="muted">{{ $c->email }}</span>
                    </td>
                    <td>{{ $c->phone ?: '—' }}</td>
                    <td>{{ $c->orders_count }}</td>
                    <td><strong>{{ number_format($ltv, 0, ',', '.') }} ₫</strong></td>
                    <td>
                        <span class="badge" style="background:{{ $tag==='VIP' ? 'var(--accent-soft)' : 'var(--surface-soft)' }};color:{{ $tag==='VIP' ? 'var(--accent)' : 'var(--ink-soft)' }}">
                            {{ $tag }}
                        </span>
                    </td>
                    <td><a class="btn btn-sm btn-outline" href="{{ route('admin.customers.show', $c) }}">Hồ sơ 360°</a></td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="fa-solid fa-users"></i>Không có khách hàng.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $customers->links('vendor.pagination.admin') }}</div>
@endsection
