@extends('layouts.admin')

@section('title', $customer->name)
@section('subtitle', 'Hồ sơ khách hàng 360° · ' . $customer->email)

@section('actions')
<a href="{{ route('admin.customers.index') }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Danh sách</a>
@endsection

@section('content')
<div class="stats">
    <div class="stat-card tone-teal">
        <div class="stat-icon"><i class="fa-solid fa-gem"></i></div>
        <div class="label">Lifetime Value</div>
        <div class="value">{{ number_format($lifetimeValue, 0, ',', '.') }} ₫</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
        <div class="label">Số đơn</div>
        <div class="value">{{ $orderCount }}</div>
    </div>
    <div class="stat-card tone-warn">
        <div class="stat-icon"><i class="fa-solid fa-tag"></i></div>
        <div class="label">Phân loại</div>
        <div class="value" style="font-size:1.2rem;">{{ $tag }}</div>
    </div>
    <div class="stat-card tone-ok">
        <div class="stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
        <div class="label">Giỏ bỏ quên</div>
        <div class="value">{{ $abandonedCart->count() }} SP</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-head"><div class="card-title">Thông tin liên hệ</div></div>
        <p style="font-size:1.05rem;"><strong>{{ $customer->name }}</strong></p>
        <p class="muted">{{ $customer->email }} · {{ $customer->phone }}</p>
        <p class="muted" style="margin-top:.35rem;">{{ $customer->address }}</p>

        <div class="card-title" style="margin:1.35rem 0 .85rem;">Lịch sử mua hàng</div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>#</th><th>Ngày</th><th>Tổng</th><th>TT</th><th></th></tr></thead>
                <tbody>
                @forelse($customer->orders as $o)
                    <tr>
                        <td><strong>#{{ $o->id }}</strong></td>
                        <td>{{ $o->created_at->format('d/m/Y') }}</td>
                        <td>{{ number_format($o->total, 0, ',', '.') }} ₫</td>
                        <td>{{ $o->status_label }}</td>
                        <td><a class="btn btn-sm btn-outline" href="{{ route('admin.orders.show', $o) }}">Xem</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state">Chưa có đơn.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:1rem;">
            <div class="card-head"><div class="card-title">Giỏ chưa thanh toán</div></div>
            @if($abandonedCart->isEmpty())
                <div class="empty-state"><i class="fa-solid fa-cart-arrow-down"></i>Giỏ trống.</div>
            @else
                <ul style="list-style:none;">
                    @foreach($abandonedCart as $cart)
                        <li style="padding:.55rem 0;border-bottom:1px solid var(--line);font-size:.9rem;">
                            <strong>{{ $cart->product?->name ?? 'SP đã xóa' }}</strong>
                            <span class="muted"> × {{ $cart->quantity }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card">
            <div class="card-head"><div class="card-title">Ghi chú tư vấn</div></div>
            <form method="POST" action="{{ route('admin.customers.notes.store', $customer) }}" style="margin-bottom:1rem;">
                @csrf
                <div class="form-group">
                    <textarea name="note" rows="3" placeholder="VD: Khách đang phân vân giữa Sigma 35mm và Sony 35mm GM" required></textarea>
                </div>
                <button class="btn" type="submit"><i class="fa-solid fa-plus"></i> Thêm ghi chú</button>
            </form>
            @forelse($notes as $note)
                <div style="padding:.75rem 0;border-top:1px solid var(--line);font-size:.9rem;">
                    <div class="muted" style="font-size:.72rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase;">{{ \Carbon\Carbon::parse($note->created_at)->format('d/m/Y H:i') }}</div>
                    <div style="margin-top:.25rem;">{{ $note->note }}</div>
                </div>
            @empty
                <p class="muted">Chưa có ghi chú.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
