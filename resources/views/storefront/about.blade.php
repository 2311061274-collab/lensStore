<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu – LensStore | Nhà phân phối ống kính chính hãng Việt Nam</title>
    <meta name="description" content="LensStore – Nhà phân phối ống kính máy ảnh chuyên nghiệp hàng đầu Việt Nam từ năm 2018. Tìm hiểu về chúng tôi, đội ngũ và sứ mệnh.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#4f46e5;--primary-hover:#4338ca;--accent:#f59e0b;--dark:#0f172a;--surface:#fff;--surface2:#f8fafc;--text-main:#1e293b;--text-muted:#64748b;--border:#e2e8f0;--radius:16px;--shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -2px rgba(0,0,0,.1);}
        *{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{font-family:'Inter',sans-serif;background:var(--surface2);color:var(--text-main);line-height:1.6;-webkit-font-smoothing:antialiased;}
        a{text-decoration:none;color:inherit;}

        /* HERO */
        .hero{background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 60%,#312e81 100%);color:white;min-height:80vh;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
        .hero::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1614935151651-0bea6508db6b?w=1600&auto=format&fit=crop&q=80') center/cover;opacity:0.1;}
        .hero-inner{max-width:1280px;margin:0 auto;padding:6rem 2.5rem;display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;position:relative;z-index:1;width:100%;}
        .hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);padding:7px 16px;border-radius:50px;font-size:0.82rem;font-weight:500;margin-bottom:1.5rem;}
        .hero-text h1{font-size:3.5rem;font-weight:900;letter-spacing:-2px;line-height:1.1;margin-bottom:1.5rem;}
        .hero-text h1 span{color:var(--accent);}
        .hero-text p{color:rgba(255,255,255,.75);font-size:1.1rem;line-height:1.9;margin-bottom:2.5rem;}
        .hero-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;}
        .hero-stat-card{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:12px;padding:1.25rem;backdrop-filter:blur(8px);}
        .hero-stat-card .num{font-size:2rem;font-weight:900;color:var(--accent);margin-bottom:4px;}
        .hero-stat-card .lbl{font-size:0.82rem;color:rgba(255,255,255,.7);}
        .hero-img-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
        .hero-img-grid img{border-radius:12px;object-fit:cover;}
        .hero-img-grid img:first-child{grid-row:1/3;height:380px;width:100%;}
        .hero-img-grid img:not(:first-child){height:180px;width:100%;}

        /* SECTION */
        .section{max-width:1280px;margin:0 auto;padding:5rem 2.5rem;}
        .section-tag{font-size:0.82rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:2px;margin-bottom:0.75rem;}
        .section-title{font-size:2.5rem;font-weight:800;letter-spacing:-1px;line-height:1.2;margin-bottom:1.5rem;}
        .section-title span{color:var(--primary);}
        .section-body{color:var(--text-muted);line-height:1.9;font-size:1.05rem;}

        /* STORY */
        .story-grid{display:grid;grid-template-columns:1fr 1fr;gap:5rem;align-items:center;}
        .story-timeline{display:flex;flex-direction:column;gap:0;}
        .timeline-item{display:flex;gap:1.25rem;padding-bottom:2rem;position:relative;}
        .timeline-item::before{content:'';position:absolute;left:20px;top:44px;bottom:0;width:2px;background:var(--border);}
        .timeline-item:last-child::before{display:none;}
        .timeline-dot{width:42px;height:42px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:white;font-size:0.9rem;font-weight:700;flex-shrink:0;}
        .timeline-content h3{font-size:1rem;font-weight:700;margin-bottom:4px;}
        .timeline-content p{font-size:0.88rem;color:var(--text-muted);line-height:1.7;}
        .timeline-year{font-size:0.75rem;font-weight:700;color:var(--primary);margin-bottom:4px;text-transform:uppercase;letter-spacing:1px;}

        /* VALUES */
        .values-section{background:linear-gradient(135deg,#0f172a,#1e1b4b);color:white;padding:5rem 2.5rem;}
        .values-inner{max-width:1280px;margin:0 auto;}
        .values-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-top:3rem;}
        .value-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:var(--radius);padding:2rem;transition:all 0.3s;}
        .value-card:hover{background:rgba(255,255,255,.1);transform:translateY(-4px);}
        .value-icon{width:52px;height:52px;border-radius:14px;background:rgba(245,158,11,.15);display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;font-size:1.3rem;color:var(--accent);}
        .value-card h3{font-size:1.1rem;font-weight:700;margin-bottom:0.75rem;}
        .value-card p{color:rgba(255,255,255,.65);font-size:0.9rem;line-height:1.7;}

        /* TEAM */
        .team-section{max-width:1280px;margin:0 auto;padding:5rem 2.5rem;}
        .team-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-top:3rem;}
        .team-card{background:white;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;text-align:center;transition:all 0.3s;}
        .team-card:hover{transform:translateY(-6px);box-shadow:0 20px 40px rgba(79,70,229,.12);}
        .team-img{width:100%;height:220px;object-fit:cover;object-position:top;}
        .team-info{padding:1.5rem;}
        .team-name{font-weight:700;font-size:1rem;margin-bottom:4px;}
        .team-role{font-size:0.82rem;color:var(--primary);font-weight:600;margin-bottom:0.75rem;}
        .team-bio{font-size:0.82rem;color:var(--text-muted);line-height:1.6;margin-bottom:1rem;}
        .team-socials{display:flex;justify-content:center;gap:10px;}
        .team-social{width:32px;height:32px;border-radius:50%;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:0.8rem;transition:all 0.2s;}
        .team-social:hover{background:var(--primary);color:white;border-color:var(--primary);}

        /* PARTNERS */
        .partners-section{background:white;border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:3rem 2.5rem;}
        .partners-inner{max-width:1280px;margin:0 auto;}
        .partners-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:1.5rem;margin-top:2.5rem;align-items:center;}
        .partner-logo{text-align:center;padding:1.5rem;border:1px solid var(--border);border-radius:12px;font-size:1.1rem;font-weight:900;color:#94a3b8;letter-spacing:2px;transition:all 0.3s;}
        .partner-logo:hover{border-color:var(--primary);color:var(--primary);transform:scale(1.05);}

        /* SHOWROOM */
        .showroom-section{max-width:1280px;margin:0 auto;padding:5rem 2.5rem;}
        .showroom-grid{display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:3rem;}
        .showroom-card{background:white;border-radius:var(--radius);border:1px solid var(--border);overflow:hidden;box-shadow:var(--shadow);}
        .showroom-img{width:100%;height:220px;object-fit:cover;}
        .showroom-info{padding:1.5rem;}
        .showroom-city{font-size:0.75rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
        .showroom-name{font-size:1.1rem;font-weight:700;margin-bottom:0.75rem;}
        .showroom-detail{display:flex;align-items:flex-start;gap:10px;font-size:0.88rem;color:var(--text-muted);margin-bottom:8px;}
        .showroom-detail i{color:var(--primary);width:16px;margin-top:2px;}

        /* CTA */
        .cta-section{background:linear-gradient(135deg,var(--primary),#6366f1);padding:5rem 2.5rem;text-align:center;color:white;}
        .cta-inner{max-width:600px;margin:0 auto;}
        .cta-inner h2{font-size:2.5rem;font-weight:900;margin-bottom:1rem;letter-spacing:-1px;}
        .cta-inner p{color:rgba(255,255,255,.8);font-size:1.05rem;margin-bottom:2.5rem;line-height:1.8;}
        .cta-btns{display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;}
        .btn-cta-primary{background:var(--accent);color:var(--dark);padding:1rem 2rem;border-radius:12px;font-weight:700;font-size:1rem;transition:all 0.3s;display:inline-flex;align-items:center;gap:8px;}
        .btn-cta-primary:hover{background:#e8ab00;transform:translateY(-2px);}
        .btn-cta-outline{background:rgba(255,255,255,.15);color:white;padding:1rem 2rem;border-radius:12px;font-weight:700;font-size:1rem;border:1px solid rgba(255,255,255,.3);transition:all 0.3s;display:inline-flex;align-items:center;gap:8px;}
        .btn-cta-outline:hover{background:rgba(255,255,255,.25);}

        /* FOOTER */
        .footer{background:var(--dark);color:rgba(255,255,255,0.7);padding:3rem 2.5rem 1.5rem;}
        .footer-inner{max-width:1280px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;}
        .footer-logo{font-size:1.3rem;font-weight:800;color:white;}
        .footer-links{display:flex;gap:1.5rem;flex-wrap:wrap;}
        .footer-links a{color:rgba(255,255,255,0.6);font-size:0.88rem;transition:color 0.2s;}
        .footer-links a:hover{color:white;}
        .footer-copy{max-width:1280px;margin:1.5rem auto 0;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.08);text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.4);}

        @media(max-width:1024px){.hero-inner,.story-grid{grid-template-columns:1fr;}.team-grid{grid-template-columns:repeat(2,1fr);}.partners-grid{grid-template-columns:repeat(3,1fr);}.showroom-grid{grid-template-columns:1fr;}.values-grid{grid-template-columns:1fr;}}
        @media(max-width:640px){.hero-text h1{font-size:2.2rem;}.section-title{font-size:1.8rem;}}
    </style>
</head>
<body>

@include('partials.storefront-navbar')

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-badge"><i class="fa-solid fa-award"></i> Thành lập từ năm 2018</div>
            <h1>Đam mê <span>Nhiếp ảnh</span>,<br>Chất lượng <span>Hàng đầu</span></h1>
            <p>LensStore không chỉ là một cửa hàng. Chúng tôi là những nhiếp ảnh gia đam mê, luôn muốn mang đến những công cụ tốt nhất để bạn tạo ra những tác phẩm đẹp nhất.</p>
            <div class="hero-stats">
                <div class="hero-stat-card"><div class="num">8+</div><div class="lbl">Năm kinh nghiệm</div></div>
                <div class="hero-stat-card"><div class="num">500+</div><div class="lbl">Sản phẩm chính hãng</div></div>
                <div class="hero-stat-card"><div class="num">12K+</div><div class="lbl">Khách hàng tin tưởng</div></div>
                <div class="hero-stat-card"><div class="num">98%</div><div class="lbl">Hài lòng với dịch vụ</div></div>
            </div>
        </div>
        <div class="hero-img-grid">
            <img src="https://images.unsplash.com/photo-1614935151651-0bea6508db6b?w=600&auto=format&fit=crop&q=80" alt="LensStore Studio">
            <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=400&auto=format&fit=crop&q=80" alt="Photography">
            <img src="https://images.unsplash.com/photo-1493863641943-9b68992a8d07?w=400&auto=format&fit=crop&q=80" alt="Camera Gear">
        </div>
    </div>
</section>

<!-- STORY -->
<div class="section">
    <div class="story-grid">
        <div>
            <div class="section-tag">Câu chuyện của chúng tôi</div>
            <h2 class="section-title">Từ niềm <span>đam mê</span> đến thương hiệu <span>hàng đầu</span></h2>
            <p class="section-body">LensStore ra đời từ một nhóm bạn trẻ có cùng niềm đam mê với nhiếp ảnh. Chúng tôi nhận ra rằng thị trường Việt Nam thiếu một cửa hàng ống kính thực sự chuyên nghiệp, nơi khách hàng có thể được tư vấn bởi những người am hiểu sâu về sản phẩm.</p>
            <p class="section-body" style="margin-top:1rem;">Ngày hôm nay, sau hơn 8 năm, LensStore đã trở thành đối tác phân phối chính thức của Canon, Sony, Nikon, Sigma và nhiều thương hiệu uy tín khác tại Việt Nam.</p>
        </div>
        <div class="story-timeline">
            @php $milestones = [
                ['year'=>'2018','icon'=>'fa-rocket','title'=>'Thành lập LensStore','desc'=>'Khai trương showroom đầu tiên tại Hà Nội với 50 sản phẩm ống kính.'],
                ['year'=>'2019','icon'=>'fa-handshake','title'=>'Đối tác chính hãng Canon & Sony','desc'=>'Ký kết hợp đồng phân phối ủy quyền với Canon và Sony Việt Nam.'],
                ['year'=>'2021','icon'=>'fa-store','title'=>'Mở rộng TP.HCM','desc'=>'Khai trương showroom thứ 2 tại TP.Hồ Chí Minh, phục vụ khách hàng miền Nam.'],
                ['year'=>'2023','icon'=>'fa-globe','title'=>'Nền tảng thương mại điện tử','desc'=>'Ra mắt website thương mại điện tử, phục vụ khách hàng toàn quốc 24/7.'],
                ['year'=>'2026','icon'=>'fa-trophy','title'=>'12.000+ khách hàng tin tưởng','desc'=>'Trở thành nhà phân phối ống kính hàng đầu Việt Nam với 12K+ khách hàng trung thành.'],
            ]; @endphp
            @foreach($milestones as $m)
            <div class="timeline-item">
                <div class="timeline-dot"><i class="fa-solid {{ $m['icon'] }}" style="font-size:0.85rem;"></i></div>
                <div class="timeline-content">
                    <div class="timeline-year">{{ $m['year'] }}</div>
                    <h3>{{ $m['title'] }}</h3>
                    <p>{{ $m['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- VALUES -->
<div class="values-section">
    <div class="values-inner">
        <div style="text-align:center;max-width:600px;margin:0 auto;">
            <div class="section-tag" style="color:rgba(255,255,255,.6);">Giá trị cốt lõi</div>
            <h2 class="section-title" style="color:white;">Những điều chúng tôi<br>luôn <span style="color:var(--accent);">cam kết</span></h2>
        </div>
        <div class="values-grid">
            @php $values = [
                ['icon'=>'fa-shield-check','title'=>'Chất lượng chính hãng 100%','desc'=>'Mọi sản phẩm đều có tem phân phối chính hãng, hóa đơn VAT đầy đủ và được bảo hành theo tiêu chuẩn nhà sản xuất.'],
                ['icon'=>'fa-users','title'=>'Tư vấn tận tâm','desc'=>'Đội ngũ 50+ chuyên viên đều là những nhiếp ảnh gia có kinh nghiệm, sẵn sàng tư vấn miễn phí không giới hạn thời gian.'],
                ['icon'=>'fa-rotate-left','title'=>'Đổi trả miễn phí 30 ngày','desc'=>'Nếu không hài lòng, chúng tôi hoàn tiền 100% trong vòng 30 ngày, không câu hỏi, không điều kiện.'],
                ['icon'=>'fa-truck-fast','title'=>'Giao hàng nhanh toàn quốc','desc'=>'Cam kết giao hàng trong 3 ngày trên toàn quốc. Đóng gói an toàn tuyệt đối bảo vệ thiết bị quý giá của bạn.'],
                ['icon'=>'fa-graduation-cap','title'=>'Cộng đồng học hỏi','desc'=>'Tổ chức workshop nhiếp ảnh miễn phí hàng tháng, kết nối cộng đồng nhiếp ảnh gia đam mê trên khắp Việt Nam.'],
                ['icon'=>'fa-headset','title'=>'Hỗ trợ 24/7','desc'=>'Tổng đài hỗ trợ hoạt động 24 giờ mỗi ngày, 7 ngày mỗi tuần. Chúng tôi luôn ở đây khi bạn cần.'],
            ]; @endphp
            @foreach($values as $v)
            <div class="value-card">
                <div class="value-icon"><i class="fa-solid {{ $v['icon'] }}"></i></div>
                <h3>{{ $v['title'] }}</h3>
                <p>{{ $v['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- TEAM -->
<div class="team-section">
    <div style="text-align:center;margin-bottom:3rem;">
        <div class="section-tag">Đội ngũ của chúng tôi</div>
        <h2 class="section-title">Những <span>chuyên gia</span> đứng sau LensStore</h2>
        <p style="color:var(--text-muted);max-width:560px;margin:0 auto;line-height:1.8;">Mỗi thành viên trong đội ngũ của chúng tôi đều là những nhiếp ảnh gia đam mê với nhiều năm kinh nghiệm thực tế.</p>
    </div>
    <div class="team-grid">
        @php $team = [
            ['name'=>'Nguyễn Minh Tuấn','role'=>'CEO & Nhiếp ảnh gia','bio'=>'10+ năm kinh nghiệm chụp ảnh sự kiện và thương mại. Người sáng lập LensStore.','img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80'],
            ['name'=>'Lê Thị Hương','role'=>'Trưởng phòng Tư vấn','bio'=>'Chuyên gia về ống kính Sony và Canon. 7 năm kinh nghiệm tư vấn thiết bị nhiếp ảnh.','img'=>'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&auto=format&fit=crop&q=80'],
            ['name'=>'Phạm Quang Minh','role'=>'Nhiếp ảnh gia kỹ thuật','bio'=>'Chuyên gia về wildlife photography. Reviewer ống kính nổi tiếng với 50K+ follower.','img'=>'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&auto=format&fit=crop&q=80'],
            ['name'=>'Trần Thị An Khoa','role'=>'Quản lý Showroom HCM','bio'=>'Nhiếp ảnh gia chân dung chuyên nghiệp, phụ trách showroom TP.HCM và khu vực miền Nam.','img'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&auto=format&fit=crop&q=80'],
        ]; @endphp
        @foreach($team as $t)
        <div class="team-card">
            <img src="{{ $t['img'] }}" alt="{{ $t['name'] }}" class="team-img">
            <div class="team-info">
                <div class="team-name">{{ $t['name'] }}</div>
                <div class="team-role">{{ $t['role'] }}</div>
                <div class="team-bio">{{ $t['bio'] }}</div>
                <div class="team-socials">
                    <a href="#" class="team-social"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="team-social"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="team-social"><i class="fa-solid fa-globe"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- PARTNERS -->
<div class="partners-section">
    <div class="partners-inner">
        <div style="text-align:center;">
            <div class="section-tag">Đối tác chính hãng</div>
            <h2 class="section-title" style="font-size:1.8rem;">Được ủy quyền bởi các thương hiệu hàng đầu</h2>
        </div>
        <div class="partners-grid">
            @foreach(['CANON','SONY','NIKON','SIGMA','ZEISS','TAMRON'] as $brand)
            <div class="partner-logo">{{ $brand }}</div>
            @endforeach
        </div>
    </div>
</div>

<!-- SHOWROOMS -->
<div class="showroom-section">
    <div style="text-align:center;margin-bottom:3rem;">
        <div class="section-tag">Showroom của chúng tôi</div>
        <h2 class="section-title">Trải nghiệm <span>trực tiếp</span> tại cửa hàng</h2>
        <p style="color:var(--text-muted);max-width:560px;margin:0 auto;line-height:1.8;">Ghé thăm showroom để được trực tiếp cầm nắm, test thử hàng trăm ống kính trước khi quyết định mua.</p>
    </div>
    <div class="showroom-grid">
        @php $showrooms = [
            ['city'=>'Hà Nội','name'=>'LensStore Hà Nội - Hoàn Kiếm','addr'=>'123 Phố Huế, Hai Bà Trưng, Hà Nội','phone'=>'024 1234 5678','hours'=>'8:00 – 21:00 hàng ngày','img'=>'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&auto=format&fit=crop&q=80'],
            ['city'=>'TP. Hồ Chí Minh','name'=>'LensStore HCM - Quận 1','addr'=>'45 Nguyễn Huệ, Bến Nghé, Q.1, TP.HCM','phone'=>'028 9876 5432','hours'=>'8:00 – 21:30 hàng ngày','img'=>'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?w=800&auto=format&fit=crop&q=80'],
        ]; @endphp
        @foreach($showrooms as $sr)
        <div class="showroom-card">
            <img src="{{ $sr['img'] }}" alt="{{ $sr['name'] }}" class="showroom-img">
            <div class="showroom-info">
                <div class="showroom-city">{{ $sr['city'] }}</div>
                <div class="showroom-name">{{ $sr['name'] }}</div>
                <div class="showroom-detail"><i class="fa-solid fa-map-pin"></i><span>{{ $sr['addr'] }}</span></div>
                <div class="showroom-detail"><i class="fa-solid fa-phone"></i><span>{{ $sr['phone'] }}</span></div>
                <div class="showroom-detail"><i class="fa-solid fa-clock"></i><span>{{ $sr['hours'] }}</span></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- CTA -->
<div class="cta-section">
    <div class="cta-inner">
        <h2>Sẵn sàng khám phá?</h2>
        <p>Hãy để chúng tôi giúp bạn tìm chiếc ống kính hoàn hảo cho phong cách nhiếp ảnh của riêng bạn.</p>
        <div class="cta-btns">
            <a href="{{ route('storefront.products') }}" class="btn-cta-primary"><i class="fa-solid fa-camera"></i> Xem sản phẩm</a>
            <a href="{{ route('storefront.support') }}" class="btn-cta-outline"><i class="fa-solid fa-headset"></i> Liên hệ tư vấn</a>
        </div>
    </div>
</div>

<!-- FOOTER -->
@include('partials.footer')

@include('partials.customer-chat')
</body>
</html>
