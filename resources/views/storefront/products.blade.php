<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm – LensStore | Ống kính máy ảnh chuyên nghiệp</title>
    <meta name="description" content="Khám phá hơn 500+ ống kính máy ảnh Canon, Sony, Nikon, Sigma chính hãng tại LensStore. Giá tốt nhất, bảo hành 2 năm.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary:#4f46e5;--primary-hover:#4338ca;--accent:#f59e0b;--dark:#0f172a;
            --surface:#fff;--surface2:#f8fafc;--text-main:#1e293b;--text-muted:#64748b;
            --border:#e2e8f0;--radius:16px;
            --shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -2px rgba(0,0,0,.1);
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{font-family:'Inter',sans-serif;background:var(--surface2);color:var(--text-main);line-height:1.6;-webkit-font-smoothing:antialiased;}
        a{text-decoration:none;color:inherit;}

        /* PAGE HERO */
        .page-hero{background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#312e81 100%);color:white;padding:5rem 2.5rem;text-align:center;position:relative;overflow:hidden;}
        .page-hero::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1617886759944-8e09b0f95f3c?w=1600&auto=format&fit=crop&q=80') center/cover;opacity:0.08;}
        .page-hero-inner{max-width:720px;margin:0 auto;position:relative;z-index:1;}
        .page-hero-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);padding:6px 14px;border-radius:50px;font-size:0.82rem;font-weight:500;margin-bottom:1.5rem;}
        .page-hero h1{font-size:3rem;font-weight:900;letter-spacing:-1.5px;margin-bottom:1rem;}
        .page-hero h1 span{color:var(--accent);}
        .page-hero p{color:rgba(255,255,255,.75);font-size:1.05rem;line-height:1.8;}

        /* FILTER SECTION */
        .filter-wrap{max-width:1280px;margin:2rem auto 0;padding:0 2.5rem;}
        .filter-card{background:white;border-radius:var(--radius);border:1px solid var(--border);padding:1.5rem 2rem;box-shadow:var(--shadow);display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;}
        .filter-group{display:flex;flex-direction:column;gap:6px;min-width:180px;}
        .filter-group label{font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);}
        .filter-group select,.filter-group input{padding:0.6rem 0.9rem;border:1px solid var(--border);border-radius:10px;font-size:0.9rem;color:var(--text-main);background:var(--surface2);transition:border-color 0.2s;}
        .filter-group select:focus,.filter-group input:focus{outline:none;border-color:var(--primary);}
        .btn-filter{background:var(--primary);color:white;padding:0.65rem 1.5rem;border-radius:10px;border:none;font-weight:600;cursor:pointer;font-size:0.9rem;transition:background 0.2s;white-space:nowrap;height:40px;align-self:flex-end;}
        .btn-filter:hover{background:var(--primary-hover);}
        .btn-clear{background:white;color:var(--text-muted);padding:0.65rem 1.2rem;border-radius:10px;border:1px solid var(--border);font-weight:600;cursor:pointer;font-size:0.9rem;height:40px;align-self:flex-end;transition:all 0.2s;}
        .btn-clear:hover{border-color:var(--primary);color:var(--primary);}

        /* PRODUCTS GRID */
        .products-wrap{max-width:1280px;margin:2rem auto 4rem;padding:0 2.5rem;}
        .products-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;}
        .products-meta h2{font-size:1.2rem;font-weight:700;}
        .products-count{font-size:0.9rem;color:var(--text-muted);background:white;padding:6px 14px;border-radius:8px;border:1px solid var(--border);}
        .product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;}
        .product-card{background:white;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;transition:all 0.3s;position:relative;display:flex;flex-direction:column;}
        .product-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(79,70,229,.12);border-color:var(--primary);}
        .product-img-wrap{position:relative;overflow:hidden;height:220px;}
        .product-img{width:100%;height:100%;object-fit:cover;transition:transform 0.4s;}
        .product-card:hover .product-img{transform:scale(1.05);}
        .product-badge{position:absolute;top:12px;left:12px;padding:4px 10px;border-radius:6px;font-size:0.72rem;font-weight:700;}
        .badge-stock{background:#ecfdf5;color:#059669;}
        .badge-oos{background:#fef2f2;color:#dc2626;}
        .badge-brand{position:absolute;top:12px;right:12px;background:rgba(15,23,42,.7);color:white;padding:3px 10px;border-radius:6px;font-size:0.7rem;font-weight:600;backdrop-filter:blur(4px);}
        .wishlist-btn{position:absolute;bottom:12px;right:12px;width:34px;height:34px;background:white;border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;color:var(--text-muted);}
        .wishlist-btn:hover{color:#ef4444;border-color:#ef4444;}
        .product-info{padding:1.25rem;display:flex;flex-direction:column;flex:1;}
        .product-cat{font-size:0.75rem;font-weight:600;color:var(--primary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;}
        .product-name{font-weight:700;font-size:0.95rem;color:var(--text-main);line-height:1.4;margin-bottom:0.75rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
        .product-specs{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:1rem;}
        .spec-chip{background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:2px 8px;font-size:0.72rem;color:var(--text-muted);font-weight:500;}
        .product-footer{display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:0.75rem;border-top:1px solid var(--surface2);}
        .product-price{font-size:1.1rem;font-weight:800;color:#ef4444;}
        .btn-add{background:var(--primary);color:white;border:none;padding:7px 14px;border-radius:8px;font-weight:600;cursor:pointer;font-size:0.82rem;display:inline-flex;align-items:center;gap:6px;transition:all 0.2s;}
        .btn-add:hover{background:var(--primary-hover);transform:scale(1.05);}
        .btn-view{background:var(--surface2);color:var(--text-main);border:1px solid var(--border);padding:7px 10px;border-radius:8px;font-weight:600;cursor:pointer;font-size:0.82rem;transition:all 0.2s;}
        .btn-view:hover{border-color:var(--primary);color:var(--primary);}

        /* PAGINATION */
        .pagination-wrap{margin-top:3rem;display:flex;justify-content:center;}
        .pagination{display:flex;gap:6px;list-style:none;}
        .pagination a,.pagination span{padding:8px 14px;border:1px solid var(--border);border-radius:9px;background:white;color:var(--text-main);font-size:0.88rem;font-weight:500;transition:all 0.2s;}
        .pagination a:hover{border-color:var(--primary);color:var(--primary);}
        .pagination .active span{background:var(--primary);color:white;border-color:var(--primary);font-weight:700;}

        /* BRANDS STRIP */
        .brands-strip{background:white;border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:2rem 2.5rem;}
        .brands-strip-inner{max-width:1280px;margin:0 auto;display:flex;align-items:center;justify-content:center;gap:3rem;flex-wrap:wrap;}
        .brand-item{font-size:1.3rem;font-weight:900;color:#cbd5e1;letter-spacing:2px;transition:color 0.3s;cursor:default;}
        .brand-item:hover{color:var(--primary);}

        /* EMPTY STATE */
        .empty{text-align:center;padding:5rem;color:var(--text-muted);}
        .empty i{font-size:3.5rem;color:#cbd5e1;margin-bottom:1.5rem;display:block;}

        /* FOOTER */
        .footer{background:var(--dark);color:rgba(255,255,255,0.7);padding:3rem 2.5rem 1.5rem;margin-top:4rem;}
        .footer-inner{max-width:1280px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;}
        .footer-logo{font-size:1.3rem;font-weight:800;color:white;}
        .footer-links{display:flex;gap:1.5rem;flex-wrap:wrap;}
        .footer-links a{color:rgba(255,255,255,0.6);font-size:0.88rem;transition:color 0.2s;}
        .footer-links a:hover{color:white;}
        .footer-copy{max-width:1280px;margin:1.5rem auto 0;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.08);text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.4);}
    </style>
</head>
<body>

@include('partials.storefront-navbar')

<!-- PAGE HERO -->
<div class="page-hero">
    <div class="page-hero-inner">
        <div class="page-hero-tag"><i class="fa-solid fa-camera"></i> Kho sản phẩm</div>
        <h1>Khám phá <span>Ống kính</span><br>Chuyên nghiệp</h1>
        <p>Hơn 500+ ống kính từ các thương hiệu hàng đầu thế giới. Bảo hành chính hãng 2 năm, giao hàng nhanh toàn quốc.</p>
    </div>
</div>

<!-- BRANDS STRIP -->
<div class="brands-strip">
    <div class="brands-strip-inner">
        @foreach(['CANON','SONY','NIKON','SIGMA','ZEISS','TAMRON','FUJIFILM','LEICA'] as $b)
            <div class="brand-item">{{ $b }}</div>
        @endforeach
    </div>
</div>

<!-- FILTER -->
<div class="filter-wrap">
    <form class="filter-card" action="{{ route('storefront.products') }}" method="GET">
        <div class="filter-group" style="flex:2;min-width:220px;">
            <label>Tìm kiếm</label>
            <input type="text" name="search" placeholder="Tên sản phẩm, thương hiệu..." value="{{ request('search') }}">
        </div>
        <div class="filter-group">
            <label>Danh mục</label>
            <select name="category">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Thương hiệu</label>
            <select name="brand">
                <option value="">Tất cả thương hiệu</option>
                @foreach($brands as $b)
                    <option value="{{ $b }}" {{ request('brand') == $b ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Sắp xếp</label>
            <select name="sort">
                <option value="newest" {{ request('sort')=='newest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="price_asc" {{ request('sort')=='price_asc' ? 'selected' : '' }}>Giá thấp → cao</option>
                <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Giá cao → thấp</option>
            </select>
        </div>
        <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Lọc</button>
        @if(request()->anyFilled(['search','category','brand','sort']))
            <a href="{{ route('storefront.products') }}" class="btn-clear"><i class="fa-solid fa-xmark"></i> Xóa lọc</a>
        @endif
    </form>
</div>

<!-- PRODUCTS -->
<div class="products-wrap">
    <div class="products-meta">
        <h2>Danh sách sản phẩm</h2>
        <span class="products-count">{{ $products->total() }} sản phẩm</span>
    </div>

    @if($products->isEmpty())
        <div class="empty">
            <i class="fa-solid fa-box-open"></i>
            <h3 style="font-size:1.3rem;margin-bottom:0.5rem;">Không tìm thấy sản phẩm</h3>
            <p>Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
        </div>
    @else
        <div class="product-grid">
            @foreach($products as $product)
            <div class="product-card">
                <div class="product-img-wrap">
                    <a href="{{ route('storefront.show', $product->id) }}">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img">
                    </a>
                    @if($product->stock > 0 && $product->status == 'in_stock')
                        <span class="product-badge badge-stock"><i class="fa-solid fa-check"></i> Còn hàng</span>
                    @else
                        <span class="product-badge badge-oos">Hết hàng</span>
                    @endif
                    @if($product->brand)
                        <span class="badge-brand">{{ $product->brand }}</span>
                    @endif
                    <button class="wishlist-btn"><i class="fa-regular fa-heart"></i></button>
                </div>
                <div class="product-info">
                    <div class="product-cat">{{ $product->category->name ?? 'Chưa phân loại' }}</div>
                    <a href="{{ route('storefront.show', $product->id) }}" class="product-name">{{ $product->name }}</a>
                    <div class="product-specs">
                        @if($product->focal_length)<span class="spec-chip"><i class="fa-solid fa-ruler-horizontal"></i> {{ $product->focal_length }}</span>@endif
                        @if($product->aperture)<span class="spec-chip"><i class="fa-solid fa-camera"></i> {{ $product->aperture }}</span>@endif
                        @if($product->mount)<span class="spec-chip"><i class="fa-solid fa-circle-notch"></i> {{ $product->mount }}</span>@endif
                    </div>
                    <div class="product-footer">
                        <div class="product-price">{{ $product->formatted_price }}</div>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('storefront.show', $product->id) }}" class="btn-view"><i class="fa-solid fa-eye"></i></a>
                            <button type="button" class="btn-add add-to-cart-btn" data-id="{{ $product->id }}">
                                <i class="fa-solid fa-cart-plus"></i> Thêm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <div class="pagination-wrap">
        {{ $products->links('vendor.pagination.storefront') }}
    </div>
</div>

<!-- FOOTER -->
@include('partials.footer')

<script>
const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
const csrfToken = '{{ csrf_token() }}';
document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!isLoggedIn) { window.location.href="{{ route('login') }}"; return; }
        const productId = this.dataset.id;
        fetch("{{ route('cart.add') }}", {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
            body:JSON.stringify({product_id:productId,quantity:1})
        }).then(r=>r.json()).then(d=>{
            if(d.success){ this.innerHTML='<i class="fa-solid fa-check"></i> Đã thêm'; setTimeout(()=>{ this.innerHTML='<i class="fa-solid fa-cart-plus"></i> Thêm'; },1500); }
        });
    });
});
</script>

@include('partials.customer-chat')
</body>
</html>
