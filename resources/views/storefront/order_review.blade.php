@extends('layouts.app')

@section('content')
<style>
    .review-container { max-width: 700px; margin: 3rem auto; padding: 0 1rem; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 2rem; border: 1px solid #e2e8f0; margin-bottom: 2rem; }
    .card-title { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; border-bottom: 2px solid #f8fafc; padding-bottom: 1rem; }
    .product-review-item { display: flex; gap: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px dashed #e2e8f0; margin-bottom: 1.5rem; }
    .product-review-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .product-thumb { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; }
    .review-form-area { flex: 1; }
    .product-name { font-weight: 700; color: #1e293b; margin-bottom: 0.8rem; font-size: 1.05rem; }
    
    .rating-group { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px; margin-bottom: 1rem; }
    .rating-group input { display: none; }
    .rating-group label { color: #cbd5e1; font-size: 1.8rem; cursor: pointer; transition: color 0.2s; }
    .rating-group label:hover, .rating-group label:hover ~ label, .rating-group input:checked ~ label { color: #f59e0b; }
    
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; }
    .form-control:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
    .btn-submit { background: #4f46e5; color: white; border: none; padding: 0.9rem 2rem; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; transition: opacity 0.2s; }
    .btn-submit:hover { opacity: 0.9; }
</style>

<div class="review-container">
    <div class="card">
        <h2 class="card-title">Đánh giá Sản phẩm</h2>
        <p style="color:#64748b; margin-bottom: 1.5rem;">Đơn hàng <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></p>

        <form action="{{ route('orders.review.store', $order->id) }}" method="POST">
            @csrf
            
            @php $index = 0; @endphp
            @foreach($order->items as $item)
                @if($item->product)
                <div class="product-review-item">
                    <img src="{{ $item->product->image_url }}" class="product-thumb">
                    <div class="review-form-area">
                        <div class="product-name">{{ $item->product_name }}</div>
                        <input type="hidden" name="reviews[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                        
                        <div class="rating-group">
                            <input type="radio" id="star5_{{ $index }}" name="reviews[{{ $index }}][rating]" value="5" checked><label for="star5_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star4_{{ $index }}" name="reviews[{{ $index }}][rating]" value="4"><label for="star4_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star3_{{ $index }}" name="reviews[{{ $index }}][rating]" value="3"><label for="star3_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star2_{{ $index }}" name="reviews[{{ $index }}][rating]" value="2"><label for="star2_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star1_{{ $index }}" name="reviews[{{ $index }}][rating]" value="1"><label for="star1_{{ $index }}"><i class="fa-solid fa-star"></i></label>
                        </div>

                        <textarea name="reviews[{{ $index }}][comment]" class="form-control" rows="3" placeholder="Hãy chia sẻ nhận xét của bạn về sản phẩm này nhé..."></textarea>
                    </div>
                </div>
                @php $index++; @endphp
                @endif
            @endforeach

            <button type="submit" class="btn-submit">Gửi Đánh Giá</button>
        </form>
    </div>
</div>
@endsection
