@extends('layouts.admin')

@section('title', 'Đơn #' . $order->id)
@section('subtitle', $order->status_label . ' · ' . $order->created_at->format('d/m/Y H:i'))

@section('actions')
<a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Danh sách</a>
@endsection

@section('content')
<div class="grid-2">
    <div class="card">
        <div class="card-head">
            <div class="card-title">Thông tin giao hàng</div>
            <span class="badge" style="background:{{ $order->status_color }}18;color:{{ $order->status_color }}">{{ $order->status_label }}</span>
        </div>
        <p style="font-size:1.05rem;"><strong>{{ $order->recipient_name }}</strong> · {{ $order->recipient_phone }}</p>
        <p class="muted" style="margin-top:.4rem;line-height:1.5;">
            {{ $order->address_detail }}, {{ $order->ward_name }}, {{ $order->district_name }}, {{ $order->province_name }}
        </p>
        <div style="margin-top:1rem;padding:1rem;background:var(--surface-soft);border-radius:12px;font-size:.9rem;">
            <div>Khách: <strong>{{ $order->user?->name }}</strong> <span class="muted">({{ $order->user?->email }})</span></div>
            <div style="margin-top:.4rem;">Thanh toán: <strong>{{ strtoupper($order->payment_method) }}</strong>
                @if($order->voucher_code)
                    · Voucher <strong>{{ $order->voucher_code }}</strong> (−{{ number_format($order->discount_amount, 0, ',', '.') }} ₫)
                @endif
            </div>
            <div style="margin-top:.4rem;">Mã GHN: <strong>{{ $order->ghn_order_code ?: 'Chưa có' }}</strong></div>
        </div>

        <div class="card-title" style="margin:1.4rem 0 .85rem;">Cập nhật trạng thái</div>
        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label>Trạng thái đơn hàng</label>
                <select name="status" id="order_status_select">
                    @foreach([
                        'pending'    => 'Chờ xác nhận',
                        'preparing'  => 'Đang chuẩn bị hàng',
                        'picked_up'  => 'Đơn vị vận chuyển đã lấy hàng',
                        'delivering' => 'Đang giao hàng',
                        'completed'  => 'Giao hàng thành công',
                        'finished'   => 'Đã hoàn thành',
                        'returning'  => 'Đang yêu cầu trả hàng / hoàn tiền',
                        'returned'   => 'Đã trả hàng / hoàn tiền',
                        'cancelled'  => 'Đã hủy',
                    ] as $k=>$v)
                        <option value="{{ $k }}" @selected($order->status === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Trạng thái thanh toán</label>
                <select name="payment_status" id="payment_status_select">
                    <option value="unpaid" @selected($order->payment_status === 'unpaid')>Chưa thanh toán</option>
                    <option value="paid" @selected($order->payment_status === 'paid')>Đã thanh toán</option>
                </select>
            </div>
            <div class="form-group">
                <label>Mã vận đơn GHN</label>
                <input type="text" name="ghn_order_code" value="{{ $order->ghn_order_code }}" placeholder="Tự tạo khi chuyển sang Đang giao">
            </div>
            <button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Lưu cập nhật</button>
            <p class="muted" style="margin-top:.65rem;font-size:.8rem;">
                Khi chọn "Đang giao" mà chưa có mã GHN, hệ thống sẽ thử tạo vận đơn qua API GHN.
            </p>
        </form>
    </div>

    <div class="card">
        <div class="card-head">
            <div class="card-title">Sản phẩm trong đơn</div>
        </div>
        <div class="table-wrap">
            <table class="data">
                <thead><tr><th>Lens</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead>
                <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td><strong>{{ $item->product_name }}</strong></td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 0, ',', '.') }} ₫</td>
                        <td>{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:1.15rem;padding-top:1rem;border-top:1px solid var(--line);text-align:right;font-size:.95rem;">
            <div class="muted">Tạm tính: {{ number_format($order->subtotal, 0, ',', '.') }} ₫</div>
            <div class="muted">Ship: {{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</div>
            @if(($order->discount_amount ?? 0) > 0)
                <div class="text-ok">Giảm: −{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</div>
            @endif
            <div style="font-family:var(--display);font-size:1.35rem;font-weight:700;margin-top:.45rem;letter-spacing:-.02em;">
                Tổng: {{ number_format($order->total, 0, ',', '.') }} ₫
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderStatus = document.getElementById('order_status_select');
        const paymentStatus = document.getElementById('payment_status_select');

        if (orderStatus && paymentStatus) {
            orderStatus.addEventListener('change', function() {
                if (this.value === 'completed' || this.value === 'finished') {
                    paymentStatus.value = 'paid';
                    // Optional: animate to show it changed
                    paymentStatus.style.transition = 'all 0.3s';
                    paymentStatus.style.backgroundColor = '#16a34a18';
                    paymentStatus.style.color = '#16a34a';
                    setTimeout(() => {
                        paymentStatus.style.backgroundColor = '';
                        paymentStatus.style.color = '';
                    }, 1000);
                }
            });
        }
    });
</script>
@endsection
