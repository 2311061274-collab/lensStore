@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #4f46e5; --surface: #fff; --surface2: #f8fafc;
        --border: #e2e8f0; --text-main: #1e293b; --text-muted: #64748b;
        --radius-lg: 16px; --radius-md: 10px;
        --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.05);
    }
    .detail-container { max-width: 980px; margin: 3rem auto; padding: 0 2rem; }
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: var(--primary); text-decoration: none; font-weight: 600; margin-bottom: 1.5rem; }
    .back-link:hover { text-decoration: underline; }
    .detail-title { font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 2rem; }
    .detail-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 2rem; }
    .card { background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border: 1px solid rgba(226,232,240,.6); padding: 1.8rem; margin-bottom: 1.5rem; }
    .card-title { font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 1.2rem; padding-bottom: 0.8rem; border-bottom: 2px solid var(--surface2); display: flex; align-items: center; gap: 8px; }
    .card-title i { color: var(--primary); }
    .info-row { display: flex; gap: 0.5rem; margin-bottom: 0.75rem; font-size: 0.95rem; }
    .info-label { color: var(--text-muted); min-width: 130px; font-weight: 500; }
    .info-value { color: var(--text-main); font-weight: 600; }
    .status-badge { display: inline-block; padding: 4px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; color: #fff; }

    /* Order items table */
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th { text-align: left; font-size: 0.82rem; color: var(--text-muted); font-weight: 600; padding: 0.6rem 0.5rem; border-bottom: 2px solid var(--border); text-transform: uppercase; letter-spacing: .5px; }
    .items-table td { padding: 1rem 0.5rem; border-bottom: 1px dashed var(--border); vertical-align: middle; }
    .items-table tr:last-child td { border-bottom: none; }
    .product-thumb { width: 55px; height: 55px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); }
    .product-name { font-weight: 700; font-size: 0.92rem; color: var(--text-main); }

    /* Summary */
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 0.6rem; font-size: 0.95rem; color: var(--text-muted); }
    .summary-total { display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--surface2); font-size: 1.25rem; font-weight: 900; color: #ef4444; }

    .btn-cancel { background: #fee2e2; color: #991b1b; border: none; padding: 0.8rem 1.5rem; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; font-size: 0.95rem; width: 100%; margin-top: 1rem; transition: background .2s; }
    .btn-cancel:hover { background: #fecaca; }

    @media (max-width: 780px) { .detail-grid { grid-template-columns: 1fr; } }
</style>

<div class="detail-container">
    <a href="{{ route('orders.index') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Quay lại đơn hàng của tôi
    </a>

    <h1 class="detail-title">
        Chi tiết đơn hàng #{{ $order->order_code ?? str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
    </h1>

    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;padding:1rem 1.5rem;border-radius:var(--radius-md);margin-bottom:1.5rem;border:1px solid #86efac;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div style="background:#fef9c3;color:#92400e;padding:1rem 1.5rem;border-radius:var(--radius-md);margin-bottom:1.5rem;border:1px solid #fde68a;">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;color:#991b1b;padding:1rem 1.5rem;border-radius:var(--radius-md);margin-bottom:1.5rem;border:1px solid #fca5a5;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="detail-grid">
        <!-- TRÁI: Sản phẩm -->
        <div>
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-bag-shopping"></i> Sản phẩm đã đặt</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>SL</th>
                            <th style="text-align:right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    @if($item->product)
                                        <img src="{{ $item->product->image_url }}" class="product-thumb" alt="{{ $item->product_name }}">
                                    @endif
                                    <div class="product-name">{{ $item->product_name }}</div>
                                </div>
                            </td>
                            <td style="color:var(--text-muted);font-size:.9rem;">{{ number_format($item->unit_price, 0, ',', '.') }} ₫</td>
                            <td style="font-weight:700;">{{ $item->quantity }}</td>
                            <td style="text-align:right;font-weight:800;color:var(--text-main);">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PHẢI: Thông tin -->
        <div>
            <!-- Tóm tắt -->
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-receipt"></i> Tóm tắt đơn hàng</div>

                <div class="info-row">
                    <span class="info-label">Trạng thái:</span>
                    <span class="status-badge" style="background: {{ $order->status_color }};">{{ $order->status_label }}</span>
                </div>

                @if($order->ghn_order_code)
                <div class="info-row">
                    <span class="info-label">Mã GHN:</span>
                    <span class="info-value" style="color: var(--primary);">{{ $order->ghn_order_code }}</span>
                </div>
                @endif

                <div class="info-row">
                    <span class="info-label">Ngày đặt:</span>
                    <span class="info-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phương thức:</span>
                    <span class="info-value">
                        @if($order->payment_method === 'cod') <i class="fa-solid fa-truck"></i> Thanh toán khi nhận hàng (COD)
                        @elseif($order->payment_method === 'momo') <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" style="height:18px;vertical-align:middle;"> Ví MoMo
                        @else <i class="fa-solid fa-building-columns"></i> Chuyển khoản ngân hàng
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Trạng thái TT:</span>
                    <span class="info-value">
                        @if(($order->payment_status ?? '') === 'paid')
                            <span style="color:#16a34a;"><i class="fa-solid fa-check-circle"></i> Đã thanh toán</span>
                        @else
                            <span style="color:#dc2626;"><i class="fa-solid fa-clock"></i> Chưa thanh toán</span>
                        @endif
                    </span>
                </div>

                <div style="margin-top: 1.5rem;">
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span>{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="summary-total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($order->total, 0, ',', '.') }} ₫</span>
                    </div>
                </div>

                @if($order->payment_method === 'momo' && ($order->payment_status ?? '') !== 'paid')
                <a href="{{ route('momo.pay-again', $order->id) }}" style="display:block;background:linear-gradient(135deg,#ae2070,#d53f8c);color:white;padding:.9rem 1.5rem;border-radius:var(--radius-md);font-weight:700;text-align:center;text-decoration:none;margin-top:1rem;transition:opacity .2s;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" style="height:20px;vertical-align:middle;margin-right:6px;"> Thanh toán ngay với MoMo
                </a>
                @endif

                @if(in_array($order->status, ['pending', 'preparing']))
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                    @csrf
                    <button type="submit" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Hủy đơn hàng
                    </button>
                </form>
                @endif

                @if($order->status == 'completed')
                <div style="display:flex; flex-direction:column; gap:8px; margin-top: 1rem;">
                    <form action="{{ route('orders.confirm-received', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn đã nhận được hàng và hài lòng với sản phẩm? Sau khi xác nhận, bạn sẽ không thể yêu cầu Trả hàng/Hoàn tiền.');" style="margin:0;">
                        @csrf
                        <button type="submit" style="width:100%; background:var(--primary); color:white; border:none; padding:0.8rem 1.5rem; border-radius:var(--radius-md); font-weight:700; cursor:pointer; font-size: 0.95rem;">Đã nhận được hàng</button>
                    </form>
                    <a href="{{ route('orders.review', $order->id) }}" style="display:block; text-align:center; text-decoration:none; background:var(--surface); color:var(--primary); border:1px solid var(--primary); padding:0.8rem 1.5rem; border-radius:var(--radius-md); font-weight:700; cursor:pointer; font-size: 0.95rem;">Đánh giá</a>
                    <a href="{{ route('orders.return', $order->id) }}" style="display:block; text-align:center; text-decoration:none; background:var(--surface); color:var(--text-main); border:1px solid var(--border); padding:0.8rem 1.5rem; border-radius:var(--radius-md); font-weight:700; cursor:pointer; font-size: 0.95rem;">Trả hàng/Hoàn tiền</a>
                </div>
                @elseif($order->status == 'finished')
                <div style="display:flex; flex-direction:column; gap:8px; margin-top: 1rem;">
                    @php $rejectedReturn = \App\Models\ReturnRequest::where('order_id', $order->id)->where('status', 'rejected')->first(); @endphp
                    @if($rejectedReturn)
                        <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:var(--radius-md); padding:1rem; color:#991b1b; font-size:0.9rem; line-height:1.5;">
                            <i class="fa-solid fa-circle-xmark"></i> <strong>Yêu cầu trả hàng bị từ chối</strong><br>
                            Cửa hàng đã từ chối yêu cầu của bạn. Đơn hàng được chuyển về trạng thái Đã hoàn thành.
                        </div>
                    @endif
                    <button style="width:100%; background:var(--surface2); color:var(--text-muted); border:1px solid var(--border); padding:0.8rem 1.5rem; border-radius:var(--radius-md); font-weight:700; cursor:not-allowed; font-size: 0.95rem;" disabled>Đã hoàn thành</button>
                    <a href="{{ route('orders.review', $order->id) }}" style="display:block; text-align:center; text-decoration:none; background:var(--surface); color:var(--primary); border:1px solid var(--primary); padding:0.8rem 1.5rem; border-radius:var(--radius-md); font-weight:700; cursor:pointer; font-size: 0.95rem;">Đánh giá</a>
                </div>
                @elseif($order->status == 'returning')
                <div style="background:#fff7ed; border:1px solid #fdba74; border-radius:var(--radius-md); padding:1rem; margin-top:1rem; color:#9a3412; font-size:0.9rem; line-height:1.5;">
                    <i class="fa-solid fa-clock-rotate-left"></i> <strong>Đang xử lý yêu cầu đổi trả / hoàn tiền</strong><br>
                    Yêu cầu của bạn đang được cửa hàng xem xét. Bạn sẽ nhận được thông báo ngay khi có kết quả.
                </div>
                @elseif($order->status == 'returned')
                @php $approvedReturn = \App\Models\ReturnRequest::where('order_id', $order->id)->where('status', 'approved')->first(); @endphp
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:var(--radius-md); padding:1rem; margin-top:1rem; color:#1e3a8a; font-size:0.9rem; line-height:1.5;">
                    <i class="fa-solid fa-truck-ramp-box"></i> <strong>Trả hàng / Hoàn tiền được chấp nhận</strong><br>
                    Đơn vị vận chuyển sẽ liên hệ với bạn sớm nhất để lấy hàng. Vui lòng ghi mã vận đơn dưới đây lên kiện hàng:
                    @if($approvedReturn && $approvedReturn->tracking_code)
                        <div style="margin-top:0.75rem; padding:0.75rem; background:#ffffff; border:2px dashed var(--primary); border-radius:8px; font-weight:900; font-size:1.25rem; color:var(--primary); text-align:center; letter-spacing:1px;">
                            {{ $approvedReturn->tracking_code }}
                        </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Địa chỉ giao hàng -->
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-location-dot"></i> Địa chỉ giao hàng</div>
                <div class="info-row"><span class="info-label">Người nhận:</span><span class="info-value">{{ $order->recipient_name }}</span></div>
                <div class="info-row"><span class="info-label">Số điện thoại:</span><span class="info-value">{{ $order->recipient_phone }}</span></div>
                <div class="info-row"><span class="info-label">Địa chỉ:</span>
                    <span class="info-value">
                        {{ $order->address_detail }},
                        {{ $order->ward_name }},
                        {{ $order->district_name }},
                        {{ $order->province_name }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
