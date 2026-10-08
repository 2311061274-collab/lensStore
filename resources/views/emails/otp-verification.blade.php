<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã xác thực LensStore</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f1f5f9;
            padding: 32px 16px;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 560px;
            margin: 0 auto;
        }

        /* TOP BRAND BAR */
        .brand-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            border-radius: 20px 20px 0 0;
            padding: 28px 36px;
            text-align: center;
        }
        .brand-name {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .brand-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .brand-icon img { width: 22px; }
        .brand-tagline {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            margin-top: 5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* CARD BODY */
        .card {
            background: #ffffff;
            padding: 40px 40px 32px;
        }

        .greeting {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .intro-text {
            font-size: 15px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        /* OTP BOX */
        .otp-section {
            text-align: center;
            margin-bottom: 28px;
        }
        .otp-label {
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 14px;
        }
        .otp-box {
            display: inline-block;
            background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border: 2px solid #c4b5fd;
            border-radius: 16px;
            padding: 22px 44px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: 12px;
            font-variant-numeric: tabular-nums;
        }
        .otp-timer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            color: #7c3aed;
            font-weight: 500;
            margin-top: 10px;
        }
        .otp-timer-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #7c3aed;
            display: inline-block;
            animation: blink 1.2s infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* DIVIDER */
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 28px 0; }

        /* HOW TO USE */
        .steps-title {
            font-size: 13px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }
        .step-row {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 12px;
        }
        .step-num {
            width: 26px; height: 26px; border-radius: 50%;
            background: #4f46e5; color: white;
            font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 2px;
        }
        .step-text { font-size: 14px; color: #475569; line-height: 1.6; }
        .step-text strong { color: #1e293b; }

        /* WARNING */
        .warning-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 4px solid #f97316;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 24px;
            font-size: 13px;
            color: #9a3412;
            line-height: 1.6;
        }
        .warning-box strong { color: #7c2d12; }

        /* FOOTER */
        .footer {
            background: #f8fafc;
            border-radius: 0 0 20px 20px;
            padding: 24px 36px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer-brand {
            font-size: 15px;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 6px;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.8;
        }
        .footer-links {
            margin-top: 12px;
        }
        .footer-links a {
            font-size: 12px;
            color: #94a3b8;
            text-decoration: none;
            margin: 0 10px;
        }
        .footer-links a:hover { color: #4f46e5; }

        /* Responsive */
        @media (max-width: 480px) {
            .card { padding: 28px 24px; }
            .brand-bar { padding: 22px 24px; }
            .otp-code { font-size: 34px; letter-spacing: 8px; }
            .otp-box { padding: 18px 28px; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- BRAND HEADER -->
    <div class="brand-bar">
        <div class="brand-name">
            📷 LensStore
        </div>
        <div class="brand-tagline">Camera & Lens Premium Store</div>
    </div>

    <!-- CARD -->
    <div class="card">
        <div class="greeting">Xin chào, {{ $user->name }}! 👋</div>
        <p class="intro-text">
            Cảm ơn bạn đã đăng ký tài khoản tại <strong>LensStore</strong>.
            Để hoàn tất đăng ký, hãy nhập mã xác thực dưới đây vào trang web.
        </p>

        <!-- OTP BOX -->
        <div class="otp-section">
            <div class="otp-label">🔐 Mã xác thực của bạn</div>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-timer">
                    <span class="otp-timer-dot"></span>
                    Có hiệu lực trong <strong style="margin-left:4px;">15 phút</strong>
                </div>
            </div>
        </div>

        <hr class="divider">

        <!-- STEPS -->
        <div class="steps-title">Cách xác thực</div>
        <div class="step-row">
            <div class="step-num">1</div>
            <div class="step-text">Quay lại trang web <strong>LensStore</strong> mà bạn vừa đăng ký</div>
        </div>
        <div class="step-row">
            <div class="step-num">2</div>
            <div class="step-text">Nhập mã <strong>{{ $otp }}</strong> vào 6 ô xác thực trên màn hình</div>
        </div>
        <div class="step-row">
            <div class="step-num">3</div>
            <div class="step-text">Nhấn <strong>"Xác nhận mã"</strong> để kích hoạt tài khoản</div>
        </div>

        <!-- WARNING -->
        <div class="warning-box">
            ⚠️ <strong>Lưu ý bảo mật:</strong> LensStore sẽ <strong>không bao giờ</strong> gọi điện hay nhắn tin
            yêu cầu bạn cung cấp mã này. Vui lòng <strong>không chia sẻ</strong> mã với bất kỳ ai.
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-brand">📷 LensStore</div>
        <div class="footer-text">
            Email này được gửi tự động từ hệ thống LensStore.<br>
            Nếu bạn không đăng ký tài khoản, vui lòng bỏ qua email này.
        </div>
        <div class="footer-links">
            <a href="#">Điều khoản dịch vụ</a>
            <a href="#">Chính sách quyền riêng tư</a>
            <a href="#">Liên hệ hỗ trợ</a>
        </div>
    </div>

</div>
</body>
</html>
