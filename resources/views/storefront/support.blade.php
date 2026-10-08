<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hỗ trợ – LensStore | Trung tâm hỗ trợ khách hàng</title>
    <meta name="description" content="Trung tâm hỗ trợ LensStore. Tìm câu trả lời nhanh chóng, liên hệ hotline, chat trực tiếp hoặc gửi yêu cầu hỗ trợ kỹ thuật.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#4f46e5;--primary-hover:#4338ca;--accent:#f59e0b;--dark:#0f172a;--surface:#fff;--surface2:#f8fafc;--text-main:#1e293b;--text-muted:#64748b;--border:#e2e8f0;--radius:16px;--shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -2px rgba(0,0,0,.1);}
        *{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{font-family:'Inter',sans-serif;background:var(--surface2);color:var(--text-main);line-height:1.6;-webkit-font-smoothing:antialiased;}
        a{text-decoration:none;color:inherit;}

        /* HERO */
        .support-hero{background:linear-gradient(135deg,#0f172a,#1e1b4b,#312e81);color:white;padding:5rem 2.5rem;text-align:center;position:relative;overflow:hidden;}
        .support-hero::before{content:'';position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=1600&auto=format&fit=crop&q=80') center/cover;opacity:0.08;}
        .support-hero-inner{max-width:700px;margin:0 auto;position:relative;z-index:1;}
        .support-hero-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);padding:7px 16px;border-radius:50px;font-size:0.82rem;font-weight:500;margin-bottom:1.5rem;}
        .support-hero h1{font-size:3rem;font-weight:900;letter-spacing:-1.5px;margin-bottom:1rem;}
        .support-hero h1 span{color:var(--accent);}
        .support-hero p{color:rgba(255,255,255,.75);font-size:1.05rem;line-height:1.8;margin-bottom:2rem;}
        .search-bar{display:flex;max-width:520px;margin:0 auto;background:white;border-radius:14px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.3);}
        .search-bar input{flex:1;padding:1rem 1.5rem;border:none;outline:none;font-size:1rem;color:var(--text-main);}
        .search-bar button{background:var(--primary);color:white;border:none;padding:0 1.5rem;cursor:pointer;font-size:1rem;font-weight:600;transition:background 0.2s;}
        .search-bar button:hover{background:var(--primary-hover);}

        /* CONTACT CHANNELS */
        .channels-wrap{max-width:1280px;margin:3rem auto 0;padding:0 2.5rem;}
        .channels-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;}
        .channel-card{background:white;border-radius:var(--radius);border:1px solid var(--border);padding:2rem;text-align:center;transition:all 0.3s;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:0.75rem;}
        .channel-card:hover{transform:translateY(-6px);box-shadow:0 20px 40px rgba(79,70,229,.12);border-color:var(--primary);}
        .channel-icon{width:60px;height:60px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-bottom:0.25rem;}
        .channel-title{font-weight:700;font-size:1rem;}
        .channel-desc{font-size:0.82rem;color:var(--text-muted);line-height:1.6;}
        .channel-action{font-size:0.85rem;font-weight:700;color:var(--primary);margin-top:0.25rem;}
        .ch-phone .channel-icon{background:#dbeafe;color:#1d4ed8;}
        .ch-chat .channel-icon{background:#d1fae5;color:#059669;}
        .ch-email .channel-icon{background:#fef3c7;color:#92400e;}
        .ch-showroom .channel-icon{background:#fce7f3;color:#be185d;}

        /* FAQ */
        .faq-wrap{max-width:1280px;margin:4rem auto;padding:0 2.5rem;display:grid;grid-template-columns:1fr 340px;gap:3rem;}
        .faq-title{font-size:1.8rem;font-weight:800;margin-bottom:0.5rem;}
        .faq-subtitle{color:var(--text-muted);margin-bottom:2rem;}
        .faq-tabs{display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.5rem;}
        .faq-tab{padding:7px 16px;border:1px solid var(--border);border-radius:50px;font-size:0.85rem;font-weight:600;cursor:pointer;color:var(--text-muted);background:white;transition:all 0.2s;}
        .faq-tab.active,.faq-tab:hover{background:var(--primary);color:white;border-color:var(--primary);}
        .faq-list{display:flex;flex-direction:column;gap:0.75rem;}
        .faq-item{background:white;border:1px solid var(--border);border-radius:12px;overflow:hidden;}
        .faq-question{display:flex;justify-content:space-between;align-items:center;padding:1.1rem 1.5rem;cursor:pointer;font-weight:600;font-size:0.95rem;transition:background 0.2s;}
        .faq-question:hover{background:var(--surface2);}
        .faq-question i{color:var(--primary);transition:transform 0.3s;font-size:0.8rem;}
        .faq-answer{padding:0 1.5rem;max-height:0;overflow:hidden;transition:max-height 0.3s ease,padding 0.3s;}
        .faq-answer.open{padding:0 1.5rem 1.25rem;max-height:300px;}
        .faq-answer p{color:var(--text-muted);font-size:0.9rem;line-height:1.8;}

        /* HELP CATEGORIES */
        .help-cats{background:white;border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;height:fit-content;position:sticky;top:90px;}
        .help-cats h3{font-size:1rem;font-weight:700;margin-bottom:1rem;padding-bottom:0.75rem;border-bottom:2px solid var(--primary);}
        .help-cat-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;font-size:0.88rem;font-weight:500;cursor:pointer;transition:all 0.2s;color:var(--text-muted);}
        .help-cat-item:hover,.help-cat-item.active{background:rgba(79,70,229,.08);color:var(--primary);}
        .help-cat-item i{width:16px;color:var(--primary);}
        .help-cat-count{margin-left:auto;font-size:0.75rem;background:var(--surface2);border:1px solid var(--border);padding:1px 7px;border-radius:10px;}

        /* TICKET FORM */
        .ticket-wrap{max-width:1280px;margin:4rem auto;padding:0 2.5rem;}
        .ticket-grid{display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:start;}
        .ticket-info h2{font-size:1.8rem;font-weight:800;margin-bottom:1rem;}
        .ticket-info p{color:var(--text-muted);line-height:1.8;margin-bottom:2rem;}
        .service-list{display:flex;flex-direction:column;gap:1rem;}
        .service-item{display:flex;gap:1rem;padding:1rem;background:white;border-radius:12px;border:1px solid var(--border);}
        .service-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1rem;}
        .service-item h4{font-weight:600;font-size:0.9rem;margin-bottom:3px;}
        .service-item p{font-size:0.82rem;color:var(--text-muted);line-height:1.5;}
        .ticket-form{background:white;border-radius:var(--radius);border:1px solid var(--border);padding:2rem;box-shadow:var(--shadow);}
        .ticket-form h3{font-size:1.2rem;font-weight:700;margin-bottom:1.5rem;}
        .form-group{margin-bottom:1.25rem;}
        .form-group label{display:block;font-size:0.82rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);margin-bottom:6px;}
        .form-group input,.form-group select,.form-group textarea{width:100%;padding:0.75rem 1rem;border:1px solid var(--border);border-radius:10px;font-size:0.9rem;color:var(--text-main);background:var(--surface2);transition:border-color 0.2s;font-family:inherit;}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--primary);background:white;}
        .form-group textarea{resize:vertical;min-height:120px;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
        .btn-submit{width:100%;padding:1rem;background:var(--primary);color:white;border:none;border-radius:10px;font-weight:700;font-size:1rem;cursor:pointer;transition:all 0.3s;display:flex;align-items:center;justify-content:center;gap:8px;}
        .btn-submit:hover{background:var(--primary-hover);transform:translateY(-1px);}

        /* POLICIES */
        .policies-wrap{background:white;border-top:1px solid var(--border);padding:4rem 2.5rem;}
        .policies-inner{max-width:1280px;margin:0 auto;}
        .policies-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-top:3rem;}
        .policy-card{padding:1.75rem;border:1px solid var(--border);border-radius:var(--radius);text-align:center;transition:all 0.3s;}
        .policy-card:hover{border-color:var(--primary);box-shadow:0 8px 20px rgba(79,70,229,.08);}
        .policy-icon{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin:0 auto 1rem;}
        .policy-card h3{font-size:0.95rem;font-weight:700;margin-bottom:0.5rem;}
        .policy-card p{font-size:0.82rem;color:var(--text-muted);line-height:1.6;}

        /* FOOTER */
        .footer{background:var(--dark);color:rgba(255,255,255,0.7);padding:3rem 2.5rem 1.5rem;margin-top:0;}
        .footer-inner{max-width:1280px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;}
        .footer-logo{font-size:1.3rem;font-weight:800;color:white;}
        .footer-links{display:flex;gap:1.5rem;flex-wrap:wrap;}
        .footer-links a{color:rgba(255,255,255,0.6);font-size:0.88rem;transition:color 0.2s;}
        .footer-links a:hover{color:white;}
        .footer-copy{max-width:1280px;margin:1.5rem auto 0;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,0.08);text-align:center;font-size:0.82rem;color:rgba(255,255,255,0.4);}

        @media(max-width:1024px){.channels-grid{grid-template-columns:repeat(2,1fr);}.faq-wrap{grid-template-columns:1fr;}.ticket-grid{grid-template-columns:1fr;}.policies-grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:640px){.channels-grid{grid-template-columns:1fr;}.support-hero h1{font-size:2rem;}}
    </style>
</head>
<body>

@include('partials.storefront-navbar')

<!-- HERO -->
<div class="support-hero">
    <div class="support-hero-inner">
        <div class="support-hero-tag"><i class="fa-solid fa-headset"></i> Trung tâm hỗ trợ</div>
        <h1>Chúng tôi luôn <span>sẵn sàng</span><br>hỗ trợ bạn</h1>
        <p>Tìm câu trả lời nhanh chóng hoặc liên hệ trực tiếp với đội ngũ hỗ trợ 24/7 của LensStore.</p>
        <div class="search-bar">
            <input type="text" placeholder="Tìm kiếm câu hỏi, chủ đề hỗ trợ...">
            <button><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
    </div>
</div>

<!-- CONTACT CHANNELS -->
<div class="channels-wrap">
    <div class="channels-grid">
        <div class="channel-card ch-phone">
            <div class="channel-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="channel-title">Gọi điện hotline</div>
            <div class="channel-desc">Hỗ trợ trực tiếp 24/7, kết nối ngay trong vài giây</div>
            <div class="channel-action">1800 1234 (Miễn phí)</div>
        </div>
        <div class="channel-card ch-chat">
            <div class="channel-icon"><i class="fa-solid fa-comments"></i></div>
            <div class="channel-title">Chat trực tiếp</div>
            <div class="channel-desc">Phản hồi trong vòng 2 phút trong giờ hành chính</div>
            <div class="channel-action">Bắt đầu chat ngay →</div>
        </div>
        <div class="channel-card ch-email">
            <div class="channel-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="channel-title">Gửi email</div>
            <div class="channel-desc">Phản hồi trong vòng 2-4 giờ làm việc</div>
            <div class="channel-action">hello@lensstore.vn</div>
        </div>
        <div class="channel-card ch-showroom">
            <div class="channel-icon"><i class="fa-solid fa-store"></i></div>
            <div class="channel-title">Đến showroom</div>
            <div class="channel-desc">Gặp trực tiếp chuyên gia tư vấn tại HN & HCM</div>
            <div class="channel-action">Xem địa chỉ →</div>
        </div>
    </div>
</div>

<!-- FAQ + SIDEBAR -->
<div class="faq-wrap">
    <div>
        <h2 class="faq-title">Câu hỏi thường gặp</h2>
        <p class="faq-subtitle">Tìm câu trả lời nhanh cho các câu hỏi phổ biến nhất của khách hàng.</p>

        <div class="faq-tabs">
            <div class="faq-tab active">Tất cả</div>
            <div class="faq-tab">Mua hàng & Thanh toán</div>
            <div class="faq-tab">Vận chuyển</div>
            <div class="faq-tab">Đổi trả & Hoàn tiền</div>
            <div class="faq-tab">Bảo hành</div>
            <div class="faq-tab">Kỹ thuật</div>
        </div>

        <div class="faq-list">
            @php $faqs = [
                ['q'=>'LensStore có bán hàng chính hãng 100% không?','a'=>'Có, tất cả sản phẩm tại LensStore đều là hàng chính hãng 100%, có tem phân phối, hóa đơn VAT và được bảo hành theo tiêu chuẩn của nhà sản xuất. Chúng tôi là đối tác phân phối ủy quyền của Canon, Sony, Nikon, Sigma tại Việt Nam.'],
                ['q'=>'Chính sách đổi trả và hoàn tiền của LensStore như thế nào?','a'=>'LensStore áp dụng chính sách đổi trả miễn phí trong vòng 30 ngày kể từ ngày nhận hàng, với điều kiện sản phẩm còn nguyên hộp, đầy đủ phụ kiện và chưa có dấu hiệu sử dụng. Hoàn tiền 100% qua phương thức thanh toán ban đầu trong 3-5 ngày làm việc.'],
                ['q'=>'Thời gian giao hàng là bao lâu?','a'=>'Với các đơn hàng nội thành Hà Nội và TP.HCM: giao trong 1-2 ngày. Với các tỉnh thành khác: 2-3 ngày làm việc. Tất cả đơn hàng đều được đóng gói cẩn thận với foam chống sốc và hộp cứng để bảo vệ thiết bị.'],
                ['q'=>'Tôi có thể test thử ống kính trước khi mua không?','a'=>'Có! Tại showroom Hà Nội và TP.HCM, bạn hoàn toàn có thể test thử bất kỳ ống kính nào trong kho. Thêm vào đó, chúng tôi còn cung cấp dịch vụ cho thuê ống kính với giá 200.000đ - 500.000đ/ngày để bạn trải nghiệm đủ lâu trước khi quyết định đầu tư.'],
                ['q'=>'Bảo hành ống kính trong bao lâu và ở đâu?','a'=>'Tất cả sản phẩm tại LensStore được bảo hành chính hãng tối thiểu 1 năm, nhiều sản phẩm cao cấp được bảo hành 2 năm. Bạn có thể gửi bảo hành tại bất kỳ trung tâm bảo hành chính hãng nào trên toàn quốc, hoặc mang trực tiếp đến showroom của chúng tôi.'],
                ['q'=>'LensStore có hỗ trợ mua hàng trả góp không?','a'=>'Có, chúng tôi hỗ trợ trả góp 0% lãi suất qua các đối tác tài chính: Akulaku, Home Credit, FE Credit với kỳ hạn từ 3 đến 24 tháng. Điều kiện: đơn hàng từ 5 triệu đồng trở lên, có CCCD và xác minh thu nhập.'],
                ['q'=>'Làm sao để vệ sinh ống kính đúng cách?','a'=>'Để vệ sinh ống kính an toàn: (1) Dùng bóng thổi khí để thổi bụi khỏi kính trước. (2) Dùng giấy thấm dầu lau nhẹ nhàng từ trong ra ngoài. (3) Dùng dung dịch vệ sinh chuyên dụng cho kính quang học. Tuyệt đối không dùng khăn giấy thông thường vì có thể làm xước coating kính. LensStore bán đầy đủ bộ vệ sinh kính chuyên nghiệp.'],
                ['q'=>'Tôi không biết chọn ống kính nào phù hợp, phải làm sao?','a'=>'Hãy liên hệ với chúng tôi! Đội ngũ chuyên viên tư vấn của LensStore đều là các nhiếp ảnh gia có kinh nghiệm, sẵn sàng tư vấn hoàn toàn miễn phí. Bạn có thể chat trực tiếp trên website, gọi hotline 1800 1234, hoặc ghé thăm showroom. Chúng tôi luôn đặt lợi ích của khách hàng lên hàng đầu.'],
            ]; @endphp

            @foreach($faqs as $i => $faq)
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq({{ $i }})">
                    <span>{{ $faq['q'] }}</span>
                    <i class="fa-solid fa-chevron-down" id="faq-icon-{{ $i }}"></i>
                </div>
                <div class="faq-answer" id="faq-ans-{{ $i }}">
                    <p>{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- SIDEBAR -->
    <div>
        <div class="help-cats">
            <h3><i class="fa-solid fa-folder-open" style="color:var(--primary);"></i> Chủ đề hỗ trợ</h3>
            @php $cats = [
                ['icon'=>'fa-credit-card','name'=>'Thanh toán & Đặt hàng','count'=>'12'],
                ['icon'=>'fa-truck','name'=>'Vận chuyển','count'=>'8'],
                ['icon'=>'fa-rotate-left','name'=>'Đổi trả & Hoàn tiền','count'=>'9'],
                ['icon'=>'fa-shield-check','name'=>'Bảo hành sản phẩm','count'=>'11'],
                ['icon'=>'fa-wrench','name'=>'Hỗ trợ kỹ thuật','count'=>'15'],
                ['icon'=>'fa-user','name'=>'Tài khoản của tôi','count'=>'6'],
                ['icon'=>'fa-tag','name'=>'Khuyến mãi & Voucher','count'=>'7'],
                ['icon'=>'fa-star','name'=>'Tư vấn chọn lens','count'=>'20'],
            ]; @endphp
            @foreach($cats as $cat)
            <div class="help-cat-item {{ $loop->first ? 'active' : '' }}">
                <i class="fa-solid {{ $cat['icon'] }}"></i>
                <span>{{ $cat['name'] }}</span>
                <span class="help-cat-count">{{ $cat['count'] }}</span>
            </div>
            @endforeach
        </div>

        <div style="background:linear-gradient(135deg,var(--primary),#6366f1);border-radius:var(--radius);padding:2rem;color:white;text-align:center;margin-top:1.5rem;">
            <i class="fa-solid fa-phone" style="font-size:2rem;color:var(--accent);margin-bottom:1rem;display:block;"></i>
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:0.5rem;">Hotline 24/7</h3>
            <p style="font-size:1.5rem;font-weight:900;color:var(--accent);margin:0.5rem 0;">1800 1234</p>
            <p style="color:rgba(255,255,255,.8);font-size:0.82rem;">Miễn phí · Không giới hạn thời gian</p>
        </div>

        <div style="background:white;border:1px solid var(--border);border-radius:var(--radius);padding:1.5rem;margin-top:1.5rem;">
            <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:1rem;"><i class="fa-solid fa-clock" style="color:var(--primary);"></i> Giờ hỗ trợ</h3>
            @php $hours = [['day'=>'Thứ 2 – Thứ 6','time'=>'8:00 – 21:00'],['day'=>'Thứ 7','time'=>'8:00 – 18:00'],['day'=>'Chủ nhật','time'=>'9:00 – 17:00'],['day'=>'Hotline','time'=>'24/7 không nghỉ']]; @endphp
            @foreach($hours as $h)
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--surface2);font-size:0.85rem;">
                <span style="color:var(--text-muted);">{{ $h['day'] }}</span>
                <span style="font-weight:600;color:{{ $loop->last ? '#059669' : 'var(--text-main)' }};">{{ $h['time'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- SUBMIT TICKET -->
<div class="ticket-wrap">
    <div style="background:white;border:1px solid var(--border);border-radius:var(--radius);padding:3rem;box-shadow:var(--shadow);">
        <div class="ticket-grid">
            <div class="ticket-info">
                <div style="font-size:0.82rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:2px;margin-bottom:0.75rem;">Gửi yêu cầu</div>
                <h2>Không tìm thấy câu trả lời?<br>Để lại lời nhắn cho chúng tôi</h2>
                <p>Đội ngũ hỗ trợ sẽ phản hồi trong vòng 2-4 giờ làm việc. Với các yêu cầu khẩn cấp, hãy gọi hotline <strong>1800 1234</strong>.</p>
                <div class="service-list">
                    <div class="service-item">
                        <div class="service-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fa-solid fa-wrench"></i></div>
                        <div>
                            <h4>Hỗ trợ kỹ thuật</h4>
                            <p>Tư vấn, sửa chữa và cấu hình thiết bị nhiếp ảnh</p>
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-icon" style="background:#d1fae5;color:#059669;"><i class="fa-solid fa-rotate-left"></i></div>
                        <div>
                            <h4>Đổi trả & Bảo hành</h4>
                            <p>Xử lý yêu cầu đổi trả, bảo hành trong 24h</p>
                        </div>
                    </div>
                    <div class="service-item">
                        <div class="service-icon" style="background:#fef3c7;color:#92400e;"><i class="fa-solid fa-graduation-cap"></i></div>
                        <div>
                            <h4>Tư vấn chọn ống kính</h4>
                            <p>Được tư vấn 1-1 bởi chuyên gia nhiếp ảnh</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ticket-form">
                <h3><i class="fa-solid fa-paper-plane" style="color:var(--primary);"></i> Gửi yêu cầu hỗ trợ</h3>
                <form>
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên *</label>
                            <input type="text" placeholder="Nguyễn Văn A">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại *</label>
                            <input type="tel" placeholder="0901 234 567">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ email *</label>
                        <input type="email" placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label>Loại yêu cầu</label>
                        <select>
                            <option>-- Chọn loại yêu cầu --</option>
                            <option>Tư vấn chọn sản phẩm</option>
                            <option>Hỗ trợ kỹ thuật</option>
                            <option>Đổi trả sản phẩm</option>
                            <option>Bảo hành</option>
                            <option>Khiếu nại</option>
                            <option>Góp ý khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nội dung *</label>
                        <textarea placeholder="Mô tả chi tiết vấn đề bạn gặp phải hoặc câu hỏi bạn muốn hỏi..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-paper-plane"></i> Gửi yêu cầu
                    </button>
                    <p style="text-align:center;font-size:0.78rem;color:var(--text-muted);margin-top:0.75rem;">Phản hồi trong vòng 2–4 giờ làm việc</p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- POLICIES -->
<div class="policies-wrap">
    <div class="policies-inner">
        <div style="text-align:center;">
            <div style="font-size:0.82rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:2px;margin-bottom:0.75rem;">Chính sách dịch vụ</div>
            <h2 style="font-size:1.8rem;font-weight:800;letter-spacing:-0.5px;">Cam kết phục vụ của LensStore</h2>
        </div>
        <div class="policies-grid">
            @php $policies = [
                ['icon'=>'fa-shield-check','color'=>'#dbeafe','icolor'=>'#1d4ed8','title'=>'Hàng chính hãng 100%','desc'=>'Tất cả sản phẩm đều có tem chính hãng, hóa đơn VAT và giấy bảo hành theo tiêu chuẩn nhà sản xuất.'],
                ['icon'=>'fa-rotate-left','color'=>'#d1fae5','icolor'=>'#059669','title'=>'Đổi trả 30 ngày','desc'=>'Hoàn trả miễn phí trong 30 ngày nếu không hài lòng, hoàn tiền 100% không điều kiện.'],
                ['icon'=>'fa-truck-fast','color'=>'#fef3c7','icolor'=>'#92400e','title'=>'Giao hàng nhanh 3 ngày','desc'=>'Cam kết giao hàng trong 3 ngày toàn quốc, đóng gói an toàn, có mã tracking theo dõi.'],
                ['icon'=>'fa-headset','color'=>'#fce7f3','icolor'=>'#be185d','title'=>'Hỗ trợ 24/7','desc'=>'Hotline miễn phí hoạt động 24/7 không nghỉ, chat trực tiếp phản hồi trong 2 phút.'],
            ]; @endphp
            @foreach($policies as $p)
            <div class="policy-card">
                <div class="policy-icon" style="background:{{ $p['color'] }};color:{{ $p['icolor'] }};"><i class="fa-solid {{ $p['icon'] }}"></i></div>
                <h3>{{ $p['title'] }}</h3>
                <p>{{ $p['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- FOOTER -->
@include('partials.footer')

<script>
function toggleFaq(i) {
    const ans = document.getElementById('faq-ans-' + i);
    const icon = document.getElementById('faq-icon-' + i);
    ans.classList.toggle('open');
    icon.style.transform = ans.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
}
</script>

@include('partials.customer-chat')
</body>
</html>
