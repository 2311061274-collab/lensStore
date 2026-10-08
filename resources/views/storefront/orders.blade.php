@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #4f46e5; --surface: #fff; --surface2: #f8fafc;
        --border: #e2e8f0; --text-main: #1e293b; --text-muted: #64748b;
        --radius-lg: 16px; --radius-md: 10px;
        --shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.05);
    }
    .orders-container { max-width: 900px; margin: 3rem auto; padding: 0 2rem; }
    .page-title { font-size: 2rem; font-weight: 800; margin-bottom: 2rem; color: var(--text-main); }
    .order-card { background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border: 1px solid rgba(226,232,240,.6); margin-bottom: 1.5rem; overflow: hidden; }
    .order-card-header { display: flex; justify-content: space-between; align-items: center; padding: 1.2rem 1.8rem; background: var(--surface2); border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 8px; }
    .order-id { font-weight: 700; font-size: 1rem; color: var(--text-main); }
    .order-date { font-size: 0.85rem; color: var(--text-muted); }
    .order-status { font-size: 0.82rem; font-weight: 700; padding: 4px 14px; border-radius: 20px; color: #fff; }
    .order-card-body { padding: 1.5rem 1.8rem; }
    .order-meta { display: flex; gap: 2rem; flex-wrap: wrap; margin-bottom: 1rem; font-size: 0.92rem; color: var(--text-muted); }
    .order-meta span { display: flex; align-items: center; gap: 6px; }
    .order-meta strong { color: var(--text-main); }
    .order-items-preview { border-top: 1px dashed var(--border); margin-top: 1rem; padding-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap; }
    .preview-img { width: 55px; height: 55px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); }
    .order-card-footer { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.8rem; border-top: 1px solid var(--border); flex-wrap: wrap; gap: 8px; }
    .order-total { font-size: 1.1rem; font-weight: 800; color: #ef4444; }
    .btn-detail { background: var(--primary); color: white; padding: 0.6rem 1.4rem; border-radius: var(--radius-md); font-weight: 600; text-decoration: none; font-size: 0.9rem; transition: opacity .2s; }
    .btn-detail:hover { opacity: .85; }
    .empty-state { text-align: center; padding: 5rem 2rem; color: var(--text-muted); }
    .empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: .4; }
    .empty-state p { font-size: 1.1rem; margin-bottom: 1.5rem; }
    .btn-shop { background: linear-gradient(135deg, var(--primary), #3b82f6); color: white; padding: 0.9rem 2rem; border-radius: var(--radius-md); font-weight: 700; text-decoration: none; display: inline-block; }
</style>

<div class="orders-container">
    <h1 class="page-title"><i class="fa-solid fa-box-open" style="color: var(--primary);"></i> Đơn hàng của tôi</h1>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid #86efac;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-bag-shopping"></i>
            <p>Bạn chưa có đơn hàng nào.</p>
            <a href="{{ route('storefront.index') }}" class="btn-shop">Mua sắm ngay</a>
        </div>
    @else
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-card-header">
                <div>
                    <div class="order-id">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if($order->ghn_order_code)
                    <div style="font-size: 0.82rem; color: var(--text-muted);">
                        <i class="fa-solid fa-truck"></i> Mã GHN: <strong>{{ $order->ghn_order_code }}</strong>
                    </div>
                @endif
                <span class="order-status" style="background: {{ $order->status_color }};">
                    {{ $order->status_label }}
                </span>
            </div>

            <div class="order-card-body">
                <div class="order-meta">
                    <span><i class="fa-solid fa-user"></i> <strong>{{ $order->recipient_name }}</strong></span>
                    <span><i class="fa-solid fa-phone"></i> <strong>{{ $order->recipient_phone }}</strong></span>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $order->ward_name }}, {{ $order->district_name }}, {{ $order->province_name }}</span>
                </div>

                <div class="order-items-preview">
                    @foreach($order->items->take(5) as $item)
                        @if($item->product)
                            <img src="{{ $item->product->image_url }}" class="preview-img" alt="{{ $item->product_name }}" title="{{ $item->product_name }} x{{ $item->quantity }}">
                        @endif
                    @endforeach
                    @if($order->items->count() > 5)
                        <div style="width:55px;height:55px;border-radius:8px;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--text-muted);font-size:0.9rem;">
                            +{{ $order->items->count() - 5 }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="order-card-footer">
                <div class="order-total">
                    Tổng: {{ number_format($order->total, 0, ',', '.') }} ₫
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    @if($order->status == 'completed')
                        <a href="{{ route('orders.review', $order->id) }}" style="text-decoration:none; background:var(--surface); color:var(--primary); border:1px solid var(--primary); padding:0.6rem 1rem; border-radius:var(--radius-md); font-weight:600; cursor:pointer;">Đánh giá</a>
                        
                        <a href="{{ route('orders.return', $order->id) }}" style="text-decoration:none; background:var(--surface); color:var(--text-main); border:1px solid var(--border); padding:0.6rem 1rem; border-radius:var(--radius-md); font-weight:600; cursor:pointer;">Trả hàng/Hoàn tiền</a>
                        
                        <form action="{{ route('orders.confirm-received', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn đã nhận được hàng và hài lòng với sản phẩm? Sau khi xác nhận, bạn sẽ không thể yêu cầu Trả hàng/Hoàn tiền.');" style="margin:0;">
                            @csrf
                            <button type="submit" style="background:var(--primary); color:white; border:none; padding:0.6rem 1rem; border-radius:var(--radius-md); font-weight:600; cursor:pointer; height:100%;">Đã nhận được hàng</button>
                        </form>
                    @elseif($order->status == 'finished')
                        <a href="{{ route('orders.review', $order->id) }}" style="text-decoration:none; background:var(--surface); color:var(--primary); border:1px solid var(--primary); padding:0.6rem 1rem; border-radius:var(--radius-md); font-weight:600; cursor:pointer;">Đánh giá</a>
                        
                        <button style="background:var(--surface2); color:var(--text-muted); border:1px solid var(--border); padding:0.6rem 1rem; border-radius:var(--radius-md); font-weight:600; cursor:not-allowed;" disabled>Đã hoàn thành</button>
                    @elseif($order->status == 'returning')
                        <span style="background:#fff7ed; color:#c2410c; border:1px solid #fdba74; padding:0.55rem 0.9rem; border-radius:var(--radius-md); font-weight:600; font-size:0.85rem; display:inline-flex; align-items:center; gap:5px;">
                            <i class="fa-solid fa-clock-rotate-left"></i> Đang chờ xử lý đổi trả
                        </span>
                    @endif
                    <a href="{{ route('orders.show', $order->id) }}" class="btn-detail" style="border: 1px solid var(--primary);">
                        Xem chi tiết <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach

        <div style="margin-top: 2rem;">
            {{ $orders->links('vendor.pagination.storefront') }}
        </div>
    @endif
</div>
@endsection
