<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LensStore - Cửa hàng Ống kính Máy ảnh Chuyên nghiệp</title>
    <meta name="description" content="LensStore - Nhà phân phối chính hãng ống kính máy ảnh Canon, Sony, Nikon, Zeiss. Chất lượng đảm bảo, giá tốt nhất.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --accent: #f59e0b;
            --dark: #0f172a;
            --surface: #ffffff;
            --surface2: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius: 16px;
            --shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -2px rgba(0,0,0,.1);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1);
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
        .navbar-brand { font-size: 1.6rem; font-weight: 800; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 10px; letter-spacing: -0.5px; }
        .navbar-brand .icon { width: 36px; height: 36px; background: var(--primary); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem;}
        .nav-right { display: flex; align-items: center; gap: 1rem; }
        .nav-right a { text-decoration: none; color: var(--text-muted); font-weight: 500; padding: 6px 12px; border-radius: 8px; transition: all 0.2s; font-size: 0.95rem;}
        .nav-right a:hover { color: var(--primary); background: rgba(79,70,229,0.08); }
        .nav-btn { background: var(--primary); color: white !important; border-radius: 10px !important; padding: 8px 18px !important; }
        .nav-btn:hover { background: var(--primary-hover); }
        .nav-user { color: var(--text-muted); font-size: 0.9rem; }
        .btn-logout { background: transparent; border: none; font-weight: 500; cursor: pointer; font-size: 0.9rem; font-family: 'Inter'; color: #ef4444; padding: 6px 12px; border-radius: 8px; transition: background 0.2s;}
        .btn-logout:hover { background: #fee2e2; }

        /* ===== HERO ===== */
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            color: white; min-height: 85vh; display: flex; align-items: center; position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background: url('https://images.unsplash.com/photo-1606986628253-06ac7e7e6cf5?w=1600&auto=format&fit=crop&q=80') center/cover;
            opacity: 0.12;
        }
        .hero-grid { max-width: 1200px; margin: 0 auto; padding: 5rem 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; position: relative; z-index: 1; width: 100%; }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 500; margin-bottom: 1.5rem; backdrop-filter: blur(10px); }
        .hero h1 { font-size: 3.5rem; font-weight: 900; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 1.5rem; }
        .hero h1 span { color: var(--accent); }
        .hero-desc { font-size: 1.1rem; color: rgba(255,255,255,0.75); line-height: 1.8; margin-bottom: 2.5rem; }
        .hero-cta { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-hero-primary { background: var(--accent); color: var(--dark); padding: 1rem 2rem; border-radius: 12px; font-weight: 700; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; }
        .btn-hero-primary:hover { background: #e8ab00; transform: translateY(-2px); box-shadow: 0 10px 25px rgba(245,158,11,0.4);}
        .btn-hero-secondary { background: rgba(255,255,255,0.1); color: white; padding: 1rem 2rem; border-radius: 12px; font-weight: 600; text-decoration: none; border: 1px solid rgba(255,255,255,0.25); transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; backdrop-filter: blur(10px);}
        .btn-hero-secondary:hover { background: rgba(255,255,255,0.2); }
        .hero-stats { display: flex; gap: 2rem; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.15); }
        .hero-stat { text-align: center; }
        .hero-stat .num { font-size: 1.8rem; font-weight: 800; color: var(--accent); }
        .hero-stat .lbl { font-size: 0.8rem; color: rgba(255,255,255,0.6); }
        .hero-img-area { position: relative; }
        .hero-img-main { width: 100%; border-radius: 20px; box-shadow: 0 40px 80px rgba(0,0,0,0.5); transform: perspective(800px) rotateY(-5deg); transition: transform 0.5s; }
        .hero-img-main:hover { transform: perspective(800px) rotateY(0deg); }
        .hero-img-badge { position: absolute; top: -15px; right: -15px; background: white; border-radius: 14px; padding: 12px 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); color: var(--dark); font-size: 0.85rem; font-weight: 600; }
        .hero-img-badge i { color: var(--accent); margin-right: 6px; }

        /* ===== FEATURES BAR ===== */
        .features-bar { background: var(--primary); }
        .features-inner { max-width: 1200px; margin: 0 auto; padding: 1.5rem 2.5rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .feature-item { display: flex; align-items: center; gap: 12px; color: white; }
        .feature-item i { font-size: 1.3rem; color: var(--accent); }
        .feature-item strong { display: block; font-size: 0.9rem; }
        .feature-item small { color: rgba(255,255,255,0.7); font-size: 0.8rem; }

        /* ===== ABOUT ===== */
        .about-section { max-width: 1200px; margin: 5rem auto; padding: 0 2.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
        .about-tag { font-size: 0.85rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1rem; }
        .about-title { font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; line-height: 1.2; margin-bottom: 1.5rem; }
        .about-body { color: var(--text-muted); line-height: 1.9; margin-bottom: 2rem; }
        .about-points { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .about-points li { display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .about-points li i { color: var(--primary); width: 20px; }
        .about-img-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .about-img-grid img { width: 100%; border-radius: 12px; object-fit: cover; }
        .about-img-grid img:first-child { grid-row: 1 / 3; height: 100%; }
        .about-img-grid img:not(:first-child) { height: 160px; }

        /* ===== SECTION HEADER ===== */
        .section-header { text-align: center; margin-bottom: 3rem; }
        .section-tag { font-size: 0.85rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 0.75rem; }
        .section-title { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.8px; }
        .section-desc { color: var(--text-muted); max-width: 550px; margin: 0.75rem auto 0; }

        /* ===== CATEGORY NAV ===== */
        .cat-nav-wrap { background: var(--surface); border-bottom: 1px solid var(--border); position: sticky; top: 72px; z-index: 90; }
        .cat-nav { max-width: 1200px; margin: 0 auto; padding: 0 2.5rem; display: flex; gap: 0.5rem; align-items: center; overflow-x: auto; }
        .cat-nav a { padding: 1rem 1rem; text-decoration: none; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; border-bottom: 2px solid transparent; transition: all 0.2s; white-space: nowrap; }
        .cat-nav a:hover, .cat-nav a.active { color: var(--primary); border-bottom-color: var(--primary); }

        /* ===== PRODUCTS ===== */
        .products-section { max-width: 1200px; margin: 0 auto; padding: 4rem 2.5rem; }
        .products-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .products-count { color: var(--text-muted); font-size: 0.9rem; }
        .search-sort { display: flex; gap: 1rem; align-items: center; }
        .search-input { padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 0.9rem; background: var(--surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2364748b' width='16' height='16'%3E%3Cpath d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' stroke='%2364748b' stroke-width='2' fill='none'/%3E%3C/svg%3E") no-repeat 10px center; outline: none; transition: border-color 0.2s; min-width: 250px;}
        .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .product-card { background: var(--surface); border-radius: var(--radius); overflow: hidden; border: 1px solid var(--border); transition: all 0.3s ease; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: transparent; }
        .product-img-wrap { position: relative; overflow: hidden; background: #f1f5f9; }
        .product-img { width: 100%; height: 240px; object-fit: cover; transition: transform 0.5s ease; }
        .product-card:hover .product-img { transform: scale(1.05); }
        .product-badge { position: absolute; top: 12px; left: 12px; padding: 5px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-stock { background: #d1fae5; color: #065f46; }
        .badge-oos { background: #fee2e2; color: #991b1b; }
        .product-info { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }
        .product-category { font-size: 0.78rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .product-name { font-size: 1.05rem; font-weight: 700; color: var(--text-main); text-decoration: none; line-height: 1.4; margin-bottom: 0.75rem; display: block; }
        .product-name:hover { color: var(--primary); }
        .product-specs { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 1rem; }
        .spec-pill { background: var(--surface2); border: 1px solid var(--border); padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; color: var(--text-muted); }
        .product-footer { margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid var(--border); }
        .product-price { font-size: 1.3rem; font-weight: 800; color: #ef4444; }
        .btn-card { background: var(--primary); color: white; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .btn-card:hover { background: var(--primary-hover); transform: scale(1.03); }

        /* ===== EMPTY STATE ===== */
        .empty-state { grid-column: 1/-1; text-align: center; padding: 5rem; }
        .empty-state i { font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem; display: block; }

        /* ===== BRANDS ===== */
        .brands-section { background: var(--surface); padding: 4rem 2.5rem; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        .brands-inner { max-width: 1200px; margin: 0 auto; }
        .brands-grid { display: flex; justify-content: center; align-items: center; gap: 4rem; flex-wrap: wrap; margin-top: 2rem; }
        .brand-logo { font-size: 1.8rem; font-weight: 900; color: #94a3b8; letter-spacing: -1px; transition: color 0.3s; }
        .brand-logo:hover { color: var(--text-main); }

        /* ===== TESTIMONIAL ===== */
        .testimonials { max-width: 1200px; margin: 5rem auto; padding: 0 2.5rem; }
        .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 2.5rem; }
        .testi-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem; }
        .testi-stars { color: var(--accent); margin-bottom: 1rem; }
        .testi-body { color: var(--text-muted); font-style: italic; line-height: 1.7; margin-bottom: 1.5rem; }
        .testi-author { display: flex; align-items: center; gap: 12px; }
        .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .testi-name { font-weight: 600; }
        .testi-role { font-size: 0.85rem; color: var(--text-muted); }

        /* ===== PAGINATION ===== */
        .pagination { display: flex; justify-content: center; margin-top: 3rem; gap: 5px; list-style: none; }
        .pagination a, .pagination span { padding: 8px 14px; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: var(--text-main); background: var(--surface); transition: all 0.2s; font-size: 0.9rem; }
        .pagination a:hover { background: var(--surface2); border-color: var(--primary); color: var(--primary); }
        .pagination .active span { background: var(--primary); color: white; border-color: var(--primary); font-weight: 600; }

        /* ===== FOOTER ===== */
        .footer { background: var(--dark); color: rgba(255,255,255,0.7); padding: 4rem 2.5rem 2rem; margin-top: 5rem; }
        .footer-inner { max-width: 1200px; margin: 0 auto; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 3rem; margin-bottom: 3rem; }
        .footer-brand { font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 1rem; }
        .footer-desc { line-height: 1.8; font-size: 0.9rem; }
        .footer-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: white; margin-bottom: 1rem; }
        .footer-links { list-style: none; }
        .footer-links li { margin-bottom: 0.5rem; }
        .footer-links a { text-decoration: none; color: rgba(255,255,255,0.6); font-size: 0.9rem; transition: color 0.2s; }
        .footer-links a:hover { color: white; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; text-align: center; font-size: 0.85rem; }

        @media (max-width: 900px) {
            .hero-grid, .about-section { grid-template-columns: 1fr; }
            .hero-img-area { display: none; }
            .hero h1 { font-size: 2.5rem; }
            .features-inner { grid-template-columns: repeat(2, 1fr); }
            .testi-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

@include('partials.storefront-navbar')

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-grid">
        <div class="hero-text">
            <div class="hero-badge"><i class="fa-solid fa-certificate" style="color: var(--accent)"></i> Phân phối chính hãng tại Việt Nam</div>
            <h1>Nhìn xa hơn.<br>Ghi lại <span>đẹp hơn</span>.</h1>
            <p class="hero-desc">LensStore - Nhà phân phối ống kính máy ảnh chuyên nghiệp. Từ Canon, Sony, Nikon đến Zeiss, Sigma — tất cả đều có mặt ở đây với chế độ bảo hành chính hãng 2 năm.</p>
            <div class="hero-cta">
                <a href="#products" class="btn-hero-primary"><i class="fa-solid fa-magnifying-glass"></i> Khám phá sản phẩm</a>
                <a href="#about" class="btn-hero-secondary"><i class="fa-solid fa-play"></i> Về chúng tôi</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><div class="num">500+</div><div class="lbl">Sản phẩm</div></div>
                <div class="hero-stat"><div class="num">12K+</div><div class="lbl">Khách hàng</div></div>
                <div class="hero-stat"><div class="num">98%</div><div class="lbl">Hài lòng</div></div>
                <div class="hero-stat"><div class="num">2 Năm</div><div class="lbl">Bảo hành</div></div>
            </div>
        </div>
        <div class="hero-img-area">
            <img src="https://images.unsplash.com/photo-1604537466608-109fa2f16c3b?w=700&auto=format&fit=crop&q=80" alt="Camera Lens" class="hero-img-main">
            <div class="hero-img-badge"><i class="fa-solid fa-shield-check"></i> Chính hãng 100%</div>
        </div>
    </div>
</section>

<!-- ===== FEATURES BAR ===== -->
<div class="features-bar">
    <div class="features-inner">
        <div class="feature-item"><i class="fa-solid fa-truck-fast"></i><div><strong>Giao hàng toàn quốc</strong><small>Giao hàng nhanh trong 3 ngày</small></div></div>
        <div class="feature-item"><i class="fa-solid fa-shield-halved"></i><div><strong>Bảo hành chính hãng</strong><small>Lên đến 2 năm toàn cầu</small></div></div>
        <div class="feature-item"><i class="fa-solid fa-rotate-left"></i><div><strong>Đổi trả miễn phí</strong><small>Trong vòng 30 ngày</small></div></div>
        <div class="feature-item"><i class="fa-solid fa-headset"></i><div><strong>Hỗ trợ 24/7</strong><small>Tư vấn chọn lens miễn phí</small></div></div>
    </div>
</div>

<!-- ===== ABOUT ===== -->
<section class="about-section" id="about">
    <div>
        <div class="about-tag">Về chúng tôi</div>
        <h2 class="about-title">LensStore — Nơi đam mê nhiếp ảnh lên ngôi</h2>
        <p class="about-body">Thành lập từ 2018, LensStore là đối tác phân phối ủy quyền của các hãng ống kính hàng đầu thế giới tại Việt Nam. Với đội ngũ hơn 50 chuyên viên am hiểu sâu về nhiếp ảnh, chúng tôi luôn sẵn sàng tư vấn cho bạn những chiếc ống kính phù hợp nhất với phong cách sáng tác và ngân sách.</p>
        <ul class="about-points">
            <li><i class="fa-solid fa-check-circle"></i> Showroom trưng bày hơn 200 loại lens tại Hà Nội & TP.HCM</li>
            <li><i class="fa-solid fa-check-circle"></i> Dịch vụ cho thuê lens để test trước khi mua</li>
            <li><i class="fa-solid fa-check-circle"></i> Workshop nhiếp ảnh miễn phí hằng tháng</li>
            <li><i class="fa-solid fa-check-circle"></i> Giá cạnh tranh, cập nhật thị trường hằng tuần</li>
        </ul>
    </div>
    <div class="about-img-grid">
        <img src="https://images.unsplash.com/photo-1614935151651-0bea6508db6b?w=400&auto=format&fit=crop&q=80" alt="Studio">
        <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=400&auto=format&fit=crop&q=80" alt="Photographer">
        <img src="https://images.unsplash.com/photo-1493863641943-9b68992a8d07?w=400&auto=format&fit=crop&q=80" alt="Camera gear">
    </div>
</section>

<!-- ===== PRODUCTS SECTION ===== -->
<section id="products" style="background: var(--surface2); padding: 3.5rem 0 4rem;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2.5rem;">

        <!-- Header -->
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 2rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:2px; margin-bottom:0.4rem;">
                    <i class="fa-solid fa-store"></i> &nbsp;Cửa hàng
                </div>
                <h2 style="font-size:2rem; font-weight:900; letter-spacing:-0.8px; color:var(--text-main); line-height:1.2;">
                    Danh sách Ống kính
                </h2>
                <div style="color:var(--text-muted); font-size:0.88rem; margin-top:0.25rem;">
                    Hiển thị <strong>{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong> trong tổng số <strong>{{ $products->total() }}</strong> sản phẩm
                    @if(request('category') || request('search'))
                        <span style="margin-left:8px; background:#ede9fe; color:var(--primary); padding:2px 10px; border-radius:20px; font-size:0.78rem; font-weight:700;">Đã lọc</span>
                    @endif
                </div>
            </div>
            <!-- Search form -->
            <form action="{{ route('storefront.index') }}" method="GET" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <div style="position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:0.85rem; pointer-events:none;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm ống kính..." style="padding: 0.6rem 1rem 0.6rem 2.4rem; border: 1.5px solid var(--border); border-radius: 11px; font-family:inherit; font-size:0.88rem; background:var(--surface); outline:none; width:220px; transition: border-color 0.2s, box-shadow 0.2s;" onfocus="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 0 0 3px rgba(79,70,229,0.1)';" onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
                </div>
                <button type="submit" style="background:var(--primary); color:white; border:none; padding:0.6rem 1.2rem; border-radius:11px; font-weight:700; cursor:pointer; font-size:0.88rem; font-family:inherit; transition:background 0.2s;" onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='var(--primary)'">
                    Tìm
                </button>
                @if(request('search') || request('category'))
                <a href="{{ route('storefront.index') }}" style="color:#ef4444; text-decoration:none; font-size:0.85rem; font-weight:600; display:inline-flex; align-items:center; gap:4px; padding:0.6rem 0.75rem; border:1px solid #fecaca; border-radius:11px; background:#fff5f5; transition: background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff5f5'">
                    <i class="fa-solid fa-xmark"></i> Xóa lọc
                </a>
                @endif
            </form>
        </div>

        <!-- Category Filter Tabs -->
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:2rem; align-items:center; padding: 1rem 1.25rem; background:var(--surface); border-radius:16px; border:1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <span style="font-size:0.75rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-right:4px; flex-shrink:0;">Danh mục:</span>
            <a href="{{ route('storefront.index', array_filter(['search' => request('search')])) }}"
               style="display:inline-flex; align-items:center; gap:6px; padding:0.42rem 1rem; border-radius:30px; font-size:0.82rem; font-weight:600; text-decoration:none; transition:all 0.2s; flex-shrink:0;
                      {{ !request('category') ? 'background:var(--primary); color:white; box-shadow:0 3px 8px rgba(79,70,229,0.25);' : 'background:var(--surface2); color:var(--text-muted); border:1px solid var(--border);' }}">
                <i class="fa-solid fa-border-all" style="font-size:0.75rem;"></i> Tất cả
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('storefront.index', array_filter(['category' => $cat->id, 'search' => request('search')])) }}"
               style="display:inline-flex; align-items:center; gap:6px; padding:0.42rem 1rem; border-radius:30px; font-size:0.82rem; font-weight:600; text-decoration:none; transition:all 0.2s; flex-shrink:0;
                      {{ request('category') == $cat->id ? 'background:var(--primary); color:white; box-shadow:0 3px 8px rgba(79,70,229,0.25);' : 'background:var(--surface2); color:var(--text-muted); border:1px solid var(--border);' }}"
               onmouseover="{{ request('category') == $cat->id ? '' : "this.style.background='#ede9fe'; this.style.color='var(--primary)'; this.style.borderColor='#c4b5fd';" }}"
               onmouseout="{{ request('category') == $cat->id ? '' : "this.style.background='var(--surface2)'; this.style.color='var(--text-muted)'; this.style.borderColor='var(--border)';" }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(285px, 1fr)); gap:1.5rem;">
            @forelse($products as $product)
            <div style="background:var(--surface); border-radius:16px; overflow:hidden; border:1px solid var(--border); transition:all 0.3s ease; display:flex; flex-direction:column;" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 20px 40px rgba(0,0,0,0.1)'; this.style.borderColor='transparent';" onmouseout="this.style.transform='none'; this.style.boxShadow='none'; this.style.borderColor='var(--border)';">
                <!-- Image -->
                <div style="position:relative; overflow:hidden; background:#f1f5f9;">
                    <a href="{{ route('storefront.show', $product->id) }}">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width:100%; height:220px; object-fit:cover; transition:transform 0.5s ease; display:block;" onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
                    </a>
                    <div style="position:absolute; top:10px; left:10px; display:flex; flex-direction:column; gap:5px;">
                        @if($product->stock > 0 && $product->status == 'in_stock')
                            <span style="background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:8px; font-size:0.73rem; font-weight:700; text-transform:uppercase; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid fa-check"></i> Còn hàng</span>
                        @else
                            <span style="background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:8px; font-size:0.73rem; font-weight:700; text-transform:uppercase;">Hết hàng</span>
                        @endif
                    </div>
                    @if($product->stock > 0 && $product->stock <= 5)
                        <div style="position:absolute; bottom:8px; right:8px; background:rgba(239,68,68,0.9); color:white; padding:3px 9px; border-radius:8px; font-size:0.7rem; font-weight:700;">
                            Chỉ còn {{ $product->stock }} cái
                        </div>
                    @endif
                </div>
                <!-- Info -->
                <div style="padding:1.25rem 1.35rem; flex-grow:1; display:flex; flex-direction:column;">
                    <div style="font-size:0.72rem; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:1.2px; margin-bottom:5px;">{{ $product->category->name ?? 'Chưa phân loại' }}</div>
                    <a href="{{ route('storefront.show', $product->id) }}" style="font-size:1rem; font-weight:700; color:var(--text-main); text-decoration:none; line-height:1.4; margin-bottom:0.65rem; display:block; transition:color 0.2s;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-main)'">{{ $product->name }}</a>
                    <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:0.9rem;">
                        @if($product->focal_length)<span style="background:var(--surface2); border:1px solid var(--border); padding:2px 9px; border-radius:20px; font-size:0.75rem; color:var(--text-muted); display:inline-flex; align-items:center; gap:3px;"><i class="fa-solid fa-ruler-horizontal" style="font-size:0.65rem;"></i> {{ $product->focal_length }}</span>@endif
                        @if($product->aperture)<span style="background:var(--surface2); border:1px solid var(--border); padding:2px 9px; border-radius:20px; font-size:0.75rem; color:var(--text-muted); display:inline-flex; align-items:center; gap:3px;"><i class="fa-solid fa-camera" style="font-size:0.65rem;"></i> {{ $product->aperture }}</span>@endif
                        @if($product->mount)<span style="background:var(--surface2); border:1px solid var(--border); padding:2px 9px; border-radius:20px; font-size:0.75rem; color:var(--text-muted); display:inline-flex; align-items:center; gap:3px;"><i class="fa-solid fa-circle-notch" style="font-size:0.65rem;"></i> {{ $product->mount }}</span>@endif
                    </div>
                    <div style="margin-top:auto; display:flex; justify-content:space-between; align-items:center; padding-top:0.9rem; border-top:1px solid var(--border);">
                        <div style="font-size:1.25rem; font-weight:900; color:#ef4444; letter-spacing:-0.5px;">{{ $product->formatted_price }}</div>
                        <div style="display:flex; gap:7px;">
                            <a href="{{ route('storefront.show', $product->id) }}" style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border:1.5px solid var(--border); border-radius:10px; background:var(--surface2); color:var(--text-muted); text-decoration:none; transition:all 0.2s;" title="Xem chi tiết" onmouseover="this.style.background='var(--primary)'; this.style.color='white'; this.style.borderColor='var(--primary)';" onmouseout="this.style.background='var(--surface2)'; this.style.color='var(--text-muted)'; this.style.borderColor='var(--border)';">
                                <i class="fa-solid fa-eye" style="font-size:0.82rem;"></i>
                            </a>
                            @if($product->stock > 0 && $product->status == 'in_stock')
                            <button type="button" class="add-to-cart-btn" data-id="{{ $product->id }}" style="display:inline-flex; align-items:center; gap:6px; padding:0 14px; height:36px; background:var(--primary); color:white; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:pointer; font-family:inherit; transition:all 0.2s;" onmouseover="this.style.background='#4338ca'; this.style.transform='scale(1.03)';" onmouseout="this.style.background='var(--primary)'; this.style.transform='scale(1)';" title="Thêm vào giỏ">
                                <i class="fa-solid fa-cart-plus" style="font-size:0.85rem;"></i> Thêm
                            </button>
                            @else
                            <button disabled style="display:inline-flex; align-items:center; gap:6px; padding:0 14px; height:36px; background:#f1f5f9; color:#94a3b8; border:none; border-radius:10px; font-weight:700; font-size:0.82rem; cursor:not-allowed; font-family:inherit;">
                                <i class="fa-solid fa-ban" style="font-size:0.85rem;"></i> Hết
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column:1/-1; text-align:center; padding:5rem 2rem; background:var(--surface); border-radius:16px; border:1px solid var(--border);">
                <i class="fa-solid fa-box-open" style="font-size:3.5rem; color:#cbd5e1; margin-bottom:1rem; display:block;"></i>
                <h3 style="font-size:1.3rem; font-weight:700; margin-bottom:0.5rem;">Không tìm thấy sản phẩm</h3>
                <p style="color:var(--text-muted); margin-bottom:1.5rem;">Hãy thử thay đổi danh mục hoặc từ khóa tìm kiếm.</p>
                <a href="{{ route('storefront.index') }}" style="background:var(--primary); color:white; padding:0.7rem 1.5rem; border-radius:10px; font-weight:700; text-decoration:none;">Xem tất cả sản phẩm</a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div style="margin-top:3rem; display:flex; justify-content:center;">
            {{ $products->links('vendor.pagination.storefront') }}
        </div>
        @endif

    </div>
</section>

<!-- ===== BRANDS ===== -->
<section class="brands-section">
    <div class="brands-inner">
        <div class="section-header">
            <div class="section-tag">Thương hiệu đối tác</div>
            <h2 class="section-title">Phân phối chính thức từ các hãng hàng đầu</h2>
        </div>
        <div class="brands-grid">
            <div class="brand-logo">CANON</div>
            <div class="brand-logo">NIKON</div>
            <div class="brand-logo">SONY</div>
            <div class="brand-logo">ZEISS</div>
            <div class="brand-logo">SIGMA</div>
            <div class="brand-logo">TAMRON</div>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS ===== -->
<section class="testimonials">
    <div class="section-header">
        <div class="section-tag">Đánh giá từ khách hàng</div>
        <h2 class="section-title">Họ nói gì về LensStore?</h2>
        @if($testimonials->count() > 0)
        <p class="section-desc">{{ $testimonials->count() }} đánh giá thực từ khách hàng đã mua hàng tại LensStore</p>
        @endif
    </div>

    @if($testimonials->count() > 0)
    <div class="testi-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
        @foreach($testimonials as $review)
        @php
            $initials = collect(explode(' ', $review->user->name ?? 'KH'))->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
            $colors = ['#4f46e5','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#be185d'];
            $color = $colors[$review->id % count($colors)];
        @endphp
        <div class="testi-card" style="position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,{{ $color }},{{ $color }}88);"></div>
            <div class="testi-stars">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                        <i class="fa-solid fa-star" style="color:#f59e0b;font-size:0.9rem;"></i>
                    @else
                        <i class="fa-regular fa-star" style="color:#e2e8f0;font-size:0.9rem;"></i>
                    @endif
                @endfor
                <span style="font-size:0.75rem;color:var(--text-muted);margin-left:6px;font-weight:600;">{{ $review->rating }}.0</span>
            </div>
            <p class="testi-body">"{{ $review->comment }}"</p>
            <div class="testi-author">
                <div class="testi-avatar" style="background:{{ $color }};flex-shrink:0;">{{ $initials }}</div>
                <div style="min-width:0;">
                    <div class="testi-name">{{ $review->user->name ?? 'Khách hàng' }}</div>
                    @if($review->product)
                    <div class="testi-role" style="display:flex;align-items:center;gap:4px;">
                        <i class="fa-solid fa-camera" style="font-size:0.7rem;color:var(--primary);"></i>
                        <a href="{{ route('storefront.show', $review->product->id) }}" style="color:var(--primary);text-decoration:none;font-weight:600;font-size:0.78rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ Str::limit($review->product->name, 40) }}</a>
                    </div>
                    @endif
                    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:2px;">{{ $review->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div style="position:absolute;bottom:1rem;right:1.5rem;opacity:0.06;font-size:4rem;line-height:1;font-family:Georgia,serif;">"</div>
        </div>
        @endforeach
    </div>
    <div style="text-align:center;margin-top:2rem;">
        <a href="{{ route('storefront.products') }}" style="display:inline-flex;align-items:center;gap:8px;color:var(--primary);font-weight:600;font-size:0.9rem;text-decoration:none;border:1px solid rgba(79,70,229,0.25);padding:0.65rem 1.5rem;border-radius:10px;transition:all 0.2s;" onmouseover="this.style.background='rgba(79,70,229,0.06)'" onmouseout="this.style.background='transparent'">
            <i class="fa-solid fa-store"></i> Mua hàng và để lại đánh giá của bạn
        </a>
    </div>
    @else
    {{-- Fallback: hiển thị lời kêu gọi đánh giá khi chưa có --}}
    <div style="text-align:center;padding:4rem 2rem;background:var(--surface);border-radius:var(--radius);border:2px dashed var(--border);">
        <i class="fa-regular fa-star" style="font-size:3.5rem;color:#e2e8f0;margin-bottom:1rem;display:block;"></i>
        <h3 style="font-size:1.4rem;font-weight:700;margin-bottom:0.75rem;">Hãy là người đánh giá đầu tiên!</h3>
        <p style="color:var(--text-muted);max-width:420px;margin:0 auto 1.5rem;line-height:1.7;">Mua hàng tại LensStore và chia sẻ trải nghiệm của bạn để giúp những khách hàng khác đưa ra lựa chọn tốt hơn.</p>
        <a href="{{ route('storefront.products') }}" style="background:var(--primary);color:white;padding:0.8rem 2rem;border-radius:10px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-bag-shopping"></i> Khám phá sản phẩm
        </a>
    </div>
    @endif
</section>

<!-- ===== FOOTER ===== -->
@include('partials.footer')

<script>
    const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    const csrfToken = '{{ csrf_token() }}';

    // ===== GIỎ HÀNG =====
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (!isLoggedIn) {
                alert('Vui lòng đăng nhập hoặc đăng ký để thêm sản phẩm vào giỏ hàng.');
                window.location.href = "{{ route('login') }}";
                return;
            }

            const productId = this.getAttribute('data-id');
            const quantity = 1;

            fetch("{{ route('cart.add') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCountEl = document.getElementById('cart-count');
                    if (cartCountEl) cartCountEl.textContent = data.cartCount;
                    alert('Đã thêm ' + quantity + ' sản phẩm vào giỏ hàng thành công!');
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(() => alert('Có lỗi xảy ra, vui lòng thử lại.'));
        });
    });

    // ===== AUTO-SCROLL KHI CHUYỂN TRANG SẢN PHẨM =====
    (function () {
        const params = new URLSearchParams(window.location.search);

        // Nếu URL có ?page= / ?category= / ?search= thì cuộn về khu vực sản phẩm
        if (params.get('page') || params.get('category') || params.get('search') || window.location.hash === '#products') {
            const section = document.getElementById('products');
            if (section) {
                // Dùng setTimeout nhỏ để chắc chắn trình duyệt đã render xong layout
                setTimeout(() => {
                    const navbarHeight = 72 + 16; // navbar sticky + khoảng thở
                    const top = section.getBoundingClientRect().top + window.scrollY - navbarHeight;
                    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
                }, 80);
            }
        }

        // Intercept toàn bộ link số trang của pagination để giữ vị trí khi load trang mới
        document.querySelectorAll('nav a[href*="page="]').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (href) {
                    // Chuyển sang URL mới kèm anchor #products để trình duyệt biết scroll đến đó
                    const separator = href.includes('?') ? '&' : '?';
                    const url = href.includes('#') ? href : href;
                    window.location.href = url + (url.includes('#') ? '' : '#products');
                }
            });
        });
    })();
</script>

@include('partials.customer-chat')
</body>
</html>

