<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - LensStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5; --primary-hover: #4338ca; --accent: #f59e0b;
            --dark: #0f172a; --surface: #ffffff; --surface2: #f8fafc;
            --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0;
            --radius: 16px; --shadow-lg: 0 20px 25px -5px rgba(0,0,0,.1),0 8px 10px -6px rgba(0,0,0,.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--surface2); color: var(--text-main); line-height: 1.6; -webkit-font-smoothing: antialiased; }

        /* ===== NAVBAR ===== */
        .navbar {
            background: rgba(255,255,255,0.95); backdrop-filter: blur(16px);
            padding: 0 2.5rem; height: 72px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 1px 0 rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 1000;
        }
        .navbar-brand { font-size: 1.6rem; font-weight: 800; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .navbar-brand .icon { width: 36px; height: 36px; background: var(--primary); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .nav-right a { text-decoration: none; color: var(--text-muted); font-weight: 500; padding: 6px 12px; border-radius: 8px; transition: all 0.2s; font-size: 0.9rem; }
        .nav-right a:hover { color: var(--primary); background: rgba(79,70,229,0.08); }
        .btn-logout { background: transparent; border: none; font-weight: 500; cursor: pointer; font-size: 0.9rem; font-family: 'Inter'; color: #ef4444; padding: 6px 12px; border-radius: 8px; }
        .btn-logout:hover { background: #fee2e2; }

        /* ===== BREADCRUMB ===== */
        .breadcrumb-bar { background: var(--surface); border-bottom: 1px solid var(--border); }
        .breadcrumb { max-width: 1200px; margin: 0 auto; padding: 1rem 2.5rem; font-size: 0.85rem; color: var(--text-muted); display: flex; align-items: center; gap: 8px; }
        .breadcrumb a { color: var(--primary); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        /* ===== HERO PRODUCT ===== */
        .product-hero { max-width: 1200px; margin: 2.5rem auto; padding: 0 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: start; }

        /* Image Section */
        .img-section { position: sticky; top: 90px; }
        .main-img-wrap { background: #f1f5f9; border-radius: var(--radius); overflow: hidden; aspect-ratio: 4/3; display: flex; align-items: center; justify-content: center; cursor: zoom-in; border: 1px solid var(--border); }
        .main-img { width: 100%; height: 100%; object-fit: contain; transition: transform 0.5s ease; }
        .main-img-wrap:hover .main-img { transform: scale(1.08); }
        .thumb-grid { display: flex; gap: 10px; margin-top: 12px; overflow-x: auto; padding-bottom: 5px; }
        .thumb { width: 80px; height: 70px; border-radius: 10px; overflow: hidden; border: 2px solid transparent; cursor: pointer; flex-shrink: 0; transition: border-color 0.2s; }
        .thumb.active, .thumb:hover { border-color: var(--primary); }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }

        /* Info Section */
        .info-section { padding-top: 0.5rem; }
        .product-category-link { display: inline-block; background: #e0e7ff; color: var(--primary); padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; text-decoration: none; margin-bottom: 1.25rem; }
        .product-title { font-size: 2.2rem; font-weight: 900; letter-spacing: -1px; line-height: 1.2; margin-bottom: 0.75rem; }
        .product-sku { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; }
        .rating-row { display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; }
        .stars { color: var(--accent); font-size: 1.1rem; }
        .rating-text { color: var(--text-muted); font-size: 0.85rem; }

        .price-box { background: linear-gradient(135deg, #fef2f2, #fff); border: 1px solid #fecaca; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; }
        .price-main { font-size: 2.5rem; font-weight: 900; color: #dc2626; letter-spacing: -1px; }
        .price-note { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; }
        .price-badge { display: inline-flex; align-items: center; gap: 5px; background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-top: 8px; }

        /* Specs Grid */
        .specs-title { font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 1rem; }
        .specs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 2rem; }
        .spec-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px; transition: border-color 0.2s; }
        .spec-card:hover { border-color: var(--primary); }
        .spec-label { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .spec-value { font-weight: 700; font-size: 1rem; }

        /* Stock & CTA */
        .stock-row { display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem; }
        .stock-badge { padding: 6px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
        .in-stock { background: #d1fae5; color: #065f46; }
        .out-of-stock { background: #fee2e2; color: #991b1b; }

        .cta-buttons { display: flex; flex-direction: column; gap: 12px; }
        .btn-buy { background: linear-gradient(135deg, var(--primary), #6366f1); color: white; padding: 1.1rem 2rem; border-radius: 14px; font-weight: 700; font-size: 1.1rem; border: none; cursor: pointer; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; }
        .btn-buy:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(79,70,229,0.35); }
        .btn-wishlist { background: transparent; border: 2px solid var(--border); color: var(--text-main); padding: 0.9rem 2rem; border-radius: 14px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s; }
        .btn-wishlist:hover { border-color: #ef4444; color: #ef4444; }
        .btn-ask { background: #fff; border: 2px solid #c7d2fe; color: #4338ca; padding: 0.9rem 2rem; border-radius: 14px; font-weight: 700; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .trust-badges { display: flex; gap: 1.5rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border); flex-wrap: wrap; }
        .trust-item { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: var(--text-muted); }
        .trust-item i { color: var(--primary); }

        /* ===== BRAND SECTION ===== */
        .brand-section { background: var(--dark); color: white; padding: 5rem 2.5rem; margin-top: 5rem; }
        .brand-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
        .brand-label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: var(--accent); margin-bottom: 1rem; }
        .brand-name { font-size: 4rem; font-weight: 900; letter-spacing: -2px; margin-bottom: 1.5rem; }
        .brand-desc { color: rgba(255,255,255,0.7); line-height: 1.9; font-size: 1.05rem; }
        .brand-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 2.5rem; }
        .brand-stat { text-align: center; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; padding: 1.5rem; }
        .brand-stat .num { font-size: 2rem; font-weight: 900; color: var(--accent); }
        .brand-stat .lbl { font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-top: 4px; }
        .brand-img { width: 100%; border-radius: 20px; box-shadow: 0 30px 60px rgba(0,0,0,0.5); }

        /* ===== GALLERY SECTION ===== */
        .gallery-section { max-width: 1200px; margin: 5rem auto; padding: 0 2.5rem; }
        .section-tag { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: var(--primary); margin-bottom: 0.5rem; }
        .section-title { font-size: 2rem; font-weight: 800; letter-spacing: -0.8px; margin-bottom: 0.5rem; }
        .section-desc { color: var(--text-muted); margin-bottom: 2.5rem; }
        .gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .gallery-item { border-radius: 12px; overflow: hidden; position: relative; cursor: pointer; }
        .gallery-item img { width: 100%; height: 220px; object-fit: cover; transition: transform 0.5s; }
        .gallery-item:hover img { transform: scale(1.07); }
        .gallery-item.tall { grid-row: span 2; }
        .gallery-item.tall img { height: 100%; }
        .gallery-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.5), transparent); opacity: 0; transition: opacity 0.3s; display: flex; align-items: flex-end; padding: 1rem; }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-caption { color: white; font-size: 0.85rem; font-weight: 500; }

        /* ===== SAMPLE PHOTOS SECTION ===== */
        .sample-section { background: var(--dark); padding: 5rem 2.5rem; margin-top: 5rem; }
        .sample-inner { max-width: 1200px; margin: 0 auto; }
        .sample-header { text-align: center; margin-bottom: 3rem; color: white; }
        .sample-header .section-tag { color: var(--accent); }
        .sample-header .section-title { color: white; }
        .sample-header .section-desc { color: rgba(255,255,255,0.6); }
        .sample-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .sample-item { border-radius: 12px; overflow: hidden; position: relative; group; cursor: pointer; }
        .sample-item img { width: 100%; height: 280px; object-fit: cover; transition: transform 0.5s; filter: brightness(0.95); }
        .sample-item:hover img { transform: scale(1.05); filter: brightness(1.05); }
        .sample-item:first-child { grid-column: span 2; }
        .sample-item:first-child img { height: 100%; min-height: 400px; }
        .sample-meta { position: absolute; bottom: 0; left: 0; right: 0; padding: 1rem; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); color: white; transform: translateY(10px); opacity: 0; transition: all 0.3s; }
        .sample-item:hover .sample-meta { transform: translateY(0); opacity: 1; }
        .sample-tag { font-size: 0.75rem; font-weight: 600; opacity: 0.8; }
        .sample-text { font-size: 0.9rem; font-weight: 600; margin-top: 2px; }

        /* ===== DESCRIPTION SECTION ===== */
        .desc-section { max-width: 1200px; margin: 5rem auto; padding: 0 2.5rem; }
        .desc-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
        .desc-tab { display: flex; border-bottom: 1px solid var(--border); }
        .desc-tab-item { padding: 1rem 2rem; font-weight: 600; cursor: pointer; color: var(--text-muted); border-bottom: 3px solid transparent; transition: all 0.2s; font-size: 0.9rem; }
        .desc-tab-item.active { color: var(--primary); border-bottom-color: var(--primary); }
        .desc-body { padding: 3rem 4rem; line-height: 1.9; color: var(--text-muted); font-size: 1.05rem; }
        .desc-body h3 { color: var(--text-main); margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 1.25rem; }
        .desc-body h3:first-child { margin-top: 0; }
        .desc-body p { margin-bottom: 1.2rem; }
        .desc-body p:last-child { margin-bottom: 0; }

        /* ===== RELATED ===== */
        .related-section { max-width: 1200px; margin: 5rem auto 3rem; padding: 0 2.5rem; }

        @media (max-width: 900px) {
            .product-hero { grid-template-columns: 1fr; gap: 2rem; }
            .img-section { position: static; }
            .brand-inner { grid-template-columns: 1fr; }
            .sample-grid { grid-template-columns: repeat(2, 1fr); }
            .sample-item:first-child { grid-column: span 2; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .product-title { font-size: 1.7rem; }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
@include('partials.storefront-navbar')

<!-- ===== BREADCRUMB ===== -->
<div class="breadcrumb-bar">
    <div class="breadcrumb">
        <a href="{{ route('storefront.index') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="/?category={{ $product->category_id }}">{{ $product->category->name ?? 'Sản phẩm' }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span>{{ $product->name }}</span>
    </div>
</div>

<!-- ===== HERO PRODUCT ===== -->
<div class="product-hero">

    <!-- IMAGE COLUMN -->
    <div class="img-section">
        @php
            $galleryImages = is_array($product->gallery_images) ? $product->gallery_images : [];
            $galleryUrls = array_map(function($item) { return is_array($item) ? ($item['url'] ?? '') : $item; }, $galleryImages);
            $allImages = array_merge([$product->image_url], $galleryUrls);
            // Fallback demo gallery images if none saved
            if (count($allImages) <= 1) {
                $allImages = [
                    $product->image_url,
                    'https://images.unsplash.com/photo-1621365268012-7d53c82d4095?w=700&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1510127034890-ba27508e9f1c?w=700&auto=format&fit=crop&q=80',
                    'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=700&auto=format&fit=crop&q=80',
                ];
            }
        @endphp

        <div class="main-img-wrap" id="mainImgWrap">
            <img src="{{ $allImages[0] }}" alt="{{ $product->name }}" class="main-img" id="mainImg">
        </div>
        <div class="thumb-grid">
            @foreach($allImages as $idx => $img)
                <div class="thumb {{ $idx == 0 ? 'active' : '' }}" onclick="switchImg('{{ $img }}', this)">
                    <img src="{{ $img }}" alt="Ảnh {{ $idx + 1 }}">
                </div>
            @endforeach
        </div>
    </div>

    <!-- INFO COLUMN -->
    <div class="info-section">
        <a href="/?category={{ $product->category_id }}" class="product-category-link">{{ $product->category->name ?? 'Ống kính' }}</a>
        <h1 class="product-title">{{ $product->name }}</h1>
        @if($product->sku)<div class="product-sku">SKU: {{ $product->sku }}</div>@endif
        
        <div class="rating-row">
            <div class="stars">★★★★★</div>
            <span class="rating-text">4.9 / 5 (127 đánh giá)</span>
        </div>

        <div class="price-box">
            <div class="price-main">{{ $product->formatted_price }}</div>
            <div class="price-note">Giá đã bao gồm VAT</div>
            <div class="price-badge"><i class="fa-solid fa-tag"></i> Giá tốt nhất thị trường</div>
        </div>

        <div class="specs-title">Thông số kỹ thuật</div>
        <div class="specs-grid">
            @if($product->focal_length)
            <div class="spec-card">
                <div class="spec-label"><i class="fa-solid fa-ruler-horizontal"></i> Tiêu cự</div>
                <div class="spec-value">{{ $product->focal_length }}</div>
            </div>
            @endif
            @if($product->aperture)
            <div class="spec-card">
                <div class="spec-label"><i class="fa-solid fa-camera"></i> Khẩu độ</div>
                <div class="spec-value">{{ $product->aperture }}</div>
            </div>
            @endif
            @if($product->mount)
            <div class="spec-card">
                <div class="spec-label"><i class="fa-solid fa-circle-notch"></i> Ngàm gắn</div>
                <div class="spec-value">{{ $product->mount }}</div>
            </div>
            @endif
            <div class="spec-card">
                <div class="spec-label"><i class="fa-solid fa-box"></i> Tồn kho</div>
                <div class="spec-value">{{ $product->stock > 0 ? $product->stock . ' chiếc' : 'Hết hàng' }}</div>
            </div>
        </div>

        <div class="stock-row">
            @if($product->stock > 0 && $product->status == 'in_stock')
                <span class="stock-badge in-stock"><i class="fa-solid fa-check-circle"></i> Còn hàng — Sẵn sàng giao</span>
            @else
                <span class="stock-badge out-of-stock"><i class="fa-solid fa-xmark-circle"></i> Tạm hết hàng</span>
            @endif
        </div>

        <div class="cta-buttons">
            <button class="btn-buy buy-now-btn" data-id="{{ $product->id }}" style="background: linear-gradient(135deg, #f97316, #ea580c); margin-bottom: -4px;">
                <i class="fa-solid fa-bolt"></i> Mua ngay
            </button>
            <button class="btn-buy add-to-cart-btn" data-id="{{ $product->id }}" style="background: var(--surface); color: var(--primary); border: 2px solid var(--primary);">
                <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ hàng
            </button>
            @php
                $inWishlist = false;
                if (auth()->check()) {
                    $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists();
                }
            @endphp
            <button class="btn-wishlist toggle-wishlist-btn" data-id="{{ $product->id }}" id="wishlist-btn-{{ $product->id }}">
                @if($inWishlist)
                    <i class="fa-solid fa-heart" style="color:#ef4444"></i> Đã thêm vào yêu thích
                @else
                    <i class="fa-regular fa-heart"></i> Thêm vào yêu thích
                @endif
            </button>
            <button type="button" class="btn-ask" id="ask-product-btn">
                <i class="fa-solid fa-comments"></i> Hỏi nhân viên về ống kính này
            </button>
        </div>

        <div class="trust-badges">
            <div class="trust-item"><i class="fa-solid fa-shield-halved"></i> Bảo hành 2 năm</div>
            <div class="trust-item"><i class="fa-solid fa-truck-fast"></i> Giao toàn quốc</div>
            <div class="trust-item"><i class="fa-solid fa-rotate-left"></i> Đổi trả 30 ngày</div>
            <div class="trust-item"><i class="fa-solid fa-certificate"></i> Chính hãng 100%</div>
        </div>
    </div>
</div>

<!-- ===== BRAND SECTION ===== -->
@php
    $brandName = $product->brand ?? strtok($product->name, ' ');
    $brandDesc = $product->brand_description ?? 'Thương hiệu ' . $brandName . ' luôn đi tiên phong trong việc tạo ra những ống kính chất lượng cao với công nghệ quang học tiên tiến nhất. Được thành lập từ nhiều thập kỷ trước, các sản phẩm của ' . $brandName . ' được hàng triệu nhiếp ảnh gia chuyên nghiệp tin tùng trên toàn thế giới. Đây là biểu tượng của sự chính xác, độ bền và khả năng tái tạo màu sắc đỉnh cao.';
@endphp
<section class="brand-section">
    <div class="brand-inner">
        <div>
            <div class="brand-label">Thương hiệu</div>
            <div class="brand-name">{{ strtoupper($brandName) }}</div>
            <p class="brand-desc">{{ $brandDesc }}</p>
            <div class="brand-stats">
                <div class="brand-stat"><div class="num">80+</div><div class="lbl">Năm kinh nghiệm</div></div>
                <div class="brand-stat"><div class="num">200+</div><div class="lbl">Model ống kính</div></div>
                <div class="brand-stat"><div class="num">5M+</div><div class="lbl">Khách hàng toàn cầu</div></div>
            </div>
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1603316851229-f2f70e21f73e?w=600&auto=format&fit=crop&q=80" alt="{{ $brandName }}" class="brand-img">
        </div>
    </div>
</section>

<!-- ===== GALLERY — CÁC GÓC ẢNH CỦA LENS ===== -->
@php
    $galleryData = is_array($product->gallery_images) ? $product->gallery_images : [];
@endphp
<section class="gallery-section">
    <div class="section-tag">Chi tiết sản phẩm</div>
    <h2 class="section-title">Các góc ảnh của ống kính</h2>
    <p class="section-desc">Xem toàn bộ thiết kế, độ hoàn thiện và chi tiết cơ học của {{ $product->name }} qua từng góc chụp.</p>
    
    <div class="gallery-grid">
        @foreach($galleryData as $idx => $img)
            <div class="gallery-item {{ $idx == 0 ? 'tall' : '' }}">
                <img src="{{ $img['url'] ?? $img }}" alt="{{ $img['cap'] ?? 'Ảnh' }}">
                <div class="gallery-overlay">
                    <span class="gallery-caption">{{ $img['cap'] ?? '' }}</span>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- ===== SAMPLE PHOTOS ===== -->
@php
    $sampleData = is_array($product->sample_images) ? $product->sample_images : [];
@endphp
<section class="sample-section">
    <div class="sample-inner">
        <div class="sample-header">
            <div class="section-tag">Ảnh mẫu thực tế</div>
            <h2 class="section-title">Chụp bởi {{ $product->name }}</h2>
            <p class="section-desc">Những bức ảnh thực tế được chụp bằng chính ống kính này — từ phong cảnh đến chân dung, từ macro đến street photography.</p>
        </div>
        <div class="sample-grid">
            @foreach($sampleData as $s)
                <div class="sample-item">
                    <img src="{{ $s['url'] ?? '' }}" alt="{{ $s['text'] ?? '' }}">
                    <div class="sample-meta">
                        <div class="sample-tag">{{ $s['tag'] ?? '' }}</div>
                        <div class="sample-text">{{ $s['text'] ?? '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== DESCRIPTION ===== -->
<div class="desc-section">
    <div class="desc-card">
        <div class="desc-tab">
            <div class="desc-tab-item active">Mô tả sản phẩm</div>
        </div>
        <div class="desc-body">
            @if($product->description)
                {!! $product->description !!}
            @else
                <p><strong>{{ $product->name }}</strong> là một trong những ống kính hàng đầu trong dòng sản phẩm của thương hiệu {{ $brandName }}, được thiết kế dành cho các nhiếp ảnh gia chuyên nghiệp và những người đam mê nhiếp ảnh nghiêm túc.</p>
                <p>Với khẩu độ tối đa <strong>{{ $product->aperture ?? 'ấn tượng' }}</strong> và tiêu cự <strong>{{ $product->focal_length ?? 'linh hoạt' }}</strong>, ống kính cho phép thu thập ánh sáng vượt trội, tạo ra độ sâu trường ảnh đẹp mắt và lý tưởng cho điều kiện thiếu sáng.</p>
                <p>Thiết kế <strong>ngàm {{ $product->mount ?? 'chuẩn' }}</strong> đảm bảo khả năng tương thích hoàn hảo với hầu hết các body máy ảnh phổ biến hiện nay. Lớp phủ chống nước, chống bụi tiêu chuẩn cùng cơ chế lấy nét tự động im lặng (Silent AF) giúp bạn tự tin chụp trong mọi điều kiện.</p>
                <p>Đây là lựa chọn xuất sắc cho nhiếp ảnh chân dung, cưới hỏi, phong cảnh và street photography. Được phân phối chính hãng bởi LensStore với đầy đủ hộp, phụ kiện và bảo hành 2 năm toàn cầu.</p>
            @endif
        </div>
    </div>
</div>


<script>
    function switchImg(src, elem) {
        document.getElementById('mainImg').src = src;
        document.querySelectorAll('.thumb').forEach(el => el.classList.remove('active'));
        elem.classList.add('active');
    }

    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    const csrfToken = '{{ csrf_token() }}';

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (!isLoggedIn) {
                alert('Vui lòng đăng nhập hoặc đăng ký để thêm sản phẩm vào giỏ hàng.');
                window.location.href = "{{ route('login') }}";
                return;
            }

            const productId = this.getAttribute('data-id');
            const quantity = 1; // Số lượng mặc định, điều chỉnh trong giỏ hàng
            
            fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const cartCountEl = document.getElementById('cart-count');
                    if(cartCountEl) {
                        cartCountEl.textContent = data.cartCount;
                    }
                    alert('Đã thêm ' + quantity + ' sản phẩm vào giỏ hàng thành công!');
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            });
        });
    });

    document.querySelectorAll('.buy-now-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (!isLoggedIn) {
                alert('Vui lòng đăng nhập hoặc đăng ký để mua hàng.');
                window.location.href = "{{ route('login') }}";
                return;
            }

            const productId = this.getAttribute('data-id');
            const quantity = 1;
            
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
            this.style.pointerEvents = 'none';

            fetch("{{ route('cart.buy-now') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                    this.innerHTML = '<i class="fa-solid fa-bolt"></i> Mua ngay';
                    this.style.pointerEvents = 'auto';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
                this.innerHTML = '<i class="fa-solid fa-bolt"></i> Mua ngay';
                this.style.pointerEvents = 'auto';
            });
        });
    });
    document.getElementById('ask-product-btn')?.addEventListener('click', function () {
        if (window.LensStoreChat) {
            window.LensStoreChat.open({
                id: {{ $product->id }},
                name: @json($product->name)
            });
        }
    });

    // Wishlist logic
    document.querySelectorAll('.toggle-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!isLoggedIn) {
                alert('Vui lòng đăng nhập để sử dụng tính năng yêu thích.');
                window.location.href = "{{ route('login') }}";
                return;
            }
            
            const productId = this.getAttribute('data-id');
            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
            this.style.pointerEvents = 'none';

            fetch("{{ route('wishlist.toggle') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                this.style.pointerEvents = 'auto';
                if (data.status === 'added') {
                    this.innerHTML = '<i class="fa-solid fa-heart" style="color:#ef4444"></i> Đã thêm vào yêu thích';
                } else if (data.status === 'removed') {
                    this.innerHTML = '<i class="fa-regular fa-heart"></i> Thêm vào yêu thích';
                }
            })
            .catch(error => {
                console.error(error);
                this.innerHTML = originalHtml;
                this.style.pointerEvents = 'auto';
            });
        });
    });
</script>

@include('partials.customer-chat')
</body>
</html>
