@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --surface: #fff;
        --surface2: #f8fafc;
        --border: #e2e8f0;
        --text-main: #1e293b;
        --text-muted: #64748b;
    }
    .wishlist-container {
        max-width: 1200px;
        margin: 3rem auto;
        padding: 0 2rem;
    }
    .wishlist-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .wishlist-title i {
        color: #ef4444;
    }
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(285px, 1fr));
        gap: 1.5rem;
    }
    .wishlist-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }
    .wishlist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
    .wishlist-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #f1f5f9;
    }
    .wishlist-info {
        padding: 1.25rem;
    }
    .wishlist-brand {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .wishlist-name {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        text-decoration: none;
        margin-bottom: 10px;
        display: block;
        line-height: 1.4;
    }
    .wishlist-price {
        font-size: 1.2rem;
        font-weight: 800;
        color: #ef4444;
        margin-bottom: 1rem;
    }
    .wishlist-actions {
        display: flex;
        gap: 8px;
    }
    .btn-cart {
        flex: 1;
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.7rem;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-remove {
        background: #fee2e2;
        color: #991b1b;
        border: none;
        width: 42px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .empty-wishlist {
        text-align: center;
        padding: 5rem 2rem;
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
    }
    .empty-wishlist i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
    .empty-wishlist h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
</style>

<div class="wishlist-container">
    <h1 class="wishlist-title"><i class="fa-solid fa-heart"></i> Danh sách Yêu thích</h1>

    @if($wishlists->count() > 0)
        <div class="wishlist-grid">
            @foreach($wishlists as $item)
                @if($item->product)
                    <div class="wishlist-card" id="wishlist-item-{{ $item->product->id }}">
                        <a href="{{ route('storefront.show', $item->product->id) }}">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="wishlist-img">
                        </a>
                        <div class="wishlist-info">
                            <div class="wishlist-brand">{{ $item->product->brand ?? 'Ống kính' }}</div>
                            <a href="{{ route('storefront.show', $item->product->id) }}" class="wishlist-name">
                                {{ $item->product->name }}
                            </a>
                            <div class="wishlist-price">{{ $item->product->formatted_price }}</div>
                            
                            <div class="wishlist-actions">
                                <button class="btn-cart add-to-cart-btn" data-id="{{ $item->product->id }}">
                                    <i class="fa-solid fa-cart-plus"></i> Thêm giỏ
                                </button>
                                <button class="btn-remove toggle-wishlist-btn" data-id="{{ $item->product->id }}" title="Bỏ yêu thích">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="empty-wishlist">
            <i class="fa-regular fa-heart"></i>
            <h3>Chưa có sản phẩm yêu thích nào</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Hãy khám phá các ống kính tuyệt vời và lưu chúng vào đây nhé!</p>
            <a href="/" style="display:inline-block; background:var(--primary); color:white; padding:0.8rem 1.5rem; border-radius:10px; font-weight:700; text-decoration:none;">
                Quay lại cửa hàng
            </a>
        </div>
    @endif
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';
    const isLoggedIn = true; // Vì route này nằm trong auth nên luôn true

    // Xử lý nút giỏ hàng
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                    let countEl = document.getElementById('cart-count');
                    if(countEl) countEl.innerText = data.cartCount;
                } else {
                    alert('Lỗi!');
                }
            });
        });
    });

    // Xử lý nút xóa yêu thích
    document.querySelectorAll('.toggle-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            if(confirm('Bạn có chắc muốn bỏ yêu thích sản phẩm này?')) {
                fetch("{{ route('wishlist.toggle') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'removed') {
                        document.getElementById('wishlist-item-' + productId).remove();
                        // Nếu xóa hết thì reload lại trang để hiện trạng thái trống
                        if (document.querySelectorAll('.wishlist-card').length === 0) {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
