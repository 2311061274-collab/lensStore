<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LensStore')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* =========================================================
           RESET & BASE
        ========================================================= */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:       #5b21b6;
            --primary-light: #7c3aed;
            --primary-glow:  rgba(124, 58, 237, 0.25);
            --accent:        #f59e0b;
            --surface:       #ffffff;
            --surface-glass: rgba(255,255,255,0.08);
            --border:        #e5e7eb;
            --border-glass:  rgba(255,255,255,0.18);
            --text:          #111827;
            --text-muted:    #6b7280;
            --danger:        #ef4444;
            --success:       #10b981;
            --radius:        14px;
            --shadow:        0 20px 60px rgba(91,33,182,0.12);
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            background: #0f0a1e;
        }

        /* =========================================================
           LEFT – Decorative panel
        ========================================================= */
        .auth-left {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem 3rem;
            overflow: hidden;
            background: linear-gradient(145deg, #0f0a1e 0%, #1a0a3e 40%, #2d1064 100%);
        }

        /* Animated blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.55;
            animation: float 8s ease-in-out infinite;
        }
        .blob-1 { width: 420px; height: 420px; background: #7c3aed; top: -120px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 320px; height: 320px; background: #4f46e5; bottom: -80px; right: -80px; animation-delay: -3s; }
        .blob-3 { width: 220px; height: 220px; background: #ec4899; top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: -5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        .left-content { position: relative; z-index: 2; text-align: center; color: white; max-width: 420px; }

        .left-logo {
            display: inline-flex; align-items: center; gap: 12px;
            font-size: 1.6rem; font-weight: 800; color: white;
            text-decoration: none; margin-bottom: 3.5rem;
            background: var(--surface-glass);
            border: 1px solid var(--border-glass);
            padding: 0.6rem 1.4rem; border-radius: 40px;
            backdrop-filter: blur(16px);
        }
        .left-logo i { color: #c4b5fd; font-size: 1.4rem; }

        .left-tagline {
            font-size: 2.6rem; font-weight: 900; line-height: 1.25;
            letter-spacing: -1.5px; margin-bottom: 1.2rem;
        }
        .left-tagline .highlight {
            background: linear-gradient(90deg, #c4b5fd, #f9a8d4);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .left-sub {
            color: rgba(255,255,255,0.65); font-size: 0.95rem;
            line-height: 1.75; margin-bottom: 3rem;
        }

        .left-features { display: flex; flex-direction: column; gap: 12px; width: 100%; }
        .feature-pill {
            display: flex; align-items: center; gap: 14px;
            background: var(--surface-glass); border: 1px solid var(--border-glass);
            border-radius: 12px; padding: 13px 18px;
            backdrop-filter: blur(12px); text-align: left;
            transition: background .2s;
        }
        .feature-pill:hover { background: rgba(255,255,255,0.13); }
        .feature-pill .pill-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(196,181,253,0.2);
            display: flex; align-items: center; justify-content: center;
            color: #c4b5fd; font-size: 1rem; flex-shrink: 0;
        }
        .feature-pill strong { display: block; font-size: 0.88rem; color: white; }
        .feature-pill small { font-size: 0.78rem; color: rgba(255,255,255,0.55); }

        /* =========================================================
           RIGHT – Form panel
        ========================================================= */
        .auth-right {
            width: 480px; min-width: 380px;
            background: var(--surface);
            display: flex; flex-direction: column; justify-content: center;
            padding: 3.5rem 3rem;
            overflow-y: auto;
        }

        /* Top badge */
        .form-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #f5f3ff; color: var(--primary);
            padding: 5px 14px; border-radius: 20px;
            font-size: 0.78rem; font-weight: 700;
            letter-spacing: .5px; text-transform: uppercase;
            margin-bottom: 1.2rem;
        }
        .form-badge i { font-size: 0.75rem; }

        .form-title {
            font-size: 2rem; font-weight: 800; letter-spacing: -1px;
            color: var(--text); margin-bottom: 0.4rem;
        }
        .form-subtitle { font-size: 0.92rem; color: var(--text-muted); margin-bottom: 2.2rem; }
        .form-subtitle a { color: var(--primary-light); font-weight: 600; text-decoration: none; }
        .form-subtitle a:hover { text-decoration: underline; }

        /* Alert */
        .alert-danger {
            background: #fef2f2; color: #991b1b;
            border: 1px solid #fecaca; border-radius: 10px;
            padding: 0.9rem 1.1rem; margin-bottom: 1.5rem;
            font-size: 0.88rem; display: flex; gap: 10px; align-items: flex-start;
        }
        .alert-danger ul { padding-left: 16px; margin-top: 4px; }

        /* Form groups */
        .form-group { margin-bottom: 1.3rem; }
        .form-group label {
            display: block; font-weight: 600; font-size: 0.875rem;
            color: var(--text); margin-bottom: 7px;
        }
        .form-group label .req { color: var(--danger); margin-left: 2px; }

        /* Input with icon + eye toggle */
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #9ca3af; font-size: 0.9rem; pointer-events: none;
            transition: color .2s;
        }
        .form-control {
            width: 100%;
            padding: 0.82rem 3rem 0.82rem 2.75rem;
            font-size: 0.95rem; font-family: 'Inter';
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            background: #fafafa; color: var(--text);
            transition: all 0.2s; outline: none;
            -webkit-appearance: none;
        }
        .form-control:focus {
            border-color: var(--primary-light);
            background: #fff;
            box-shadow: 0 0 0 4px var(--primary-glow);
        }
        .form-control:focus ~ .input-icon { color: var(--primary-light); }
        .form-control.is-error { border-color: var(--danger); }
        .form-control::placeholder { color: #c4c4c4; }

        /* no right padding needed when no eye */
        .form-control.no-eye { padding-right: 1rem; }

        /* Eye toggle button */
        .eye-toggle {
            position: absolute; right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #9ca3af; font-size: 0.92rem; padding: 4px;
            line-height: 1; transition: color .2s;
        }
        .eye-toggle:hover { color: var(--primary-light); }

        /* Help text */
        .help-text { font-size: 0.77rem; color: var(--text-muted); margin-top: 5px; }

        /* Submit btn */
        .btn-primary {
            width: 100%; padding: 0.9rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white; border: none; border-radius: var(--radius);
            font-weight: 700; font-size: 1rem; font-family: 'Inter';
            cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 9px;
            box-shadow: 0 6px 20px var(--primary-glow);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 28px var(--primary-glow); }
        .btn-primary:active { transform: translateY(0); }

        .btn-secondary {
            width: 100%; padding: 0.88rem;
            background: transparent; color: var(--text);
            border: 1.5px solid var(--border); border-radius: var(--radius);
            font-weight: 600; font-size: 0.95rem; font-family: 'Inter';
            cursor: pointer; transition: all .2s;
        }
        .btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; }

        .btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 1.5rem; }

        .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.88rem; color: var(--text-muted); }
        .auth-footer a { color: var(--primary-light); font-weight: 600; text-decoration: none; }

        /* Divider */
        .divider { display: flex; align-items: center; gap: 12px; margin: 1.5rem 0; color: #d1d5db; font-size: 0.8rem; }
        .divider::before, .divider::after { content:''; flex:1; height:1px; background: var(--border); }

        /* =========================================================
           EXTRA YIELD AREA (steps etc.)
        ========================================================= */
        @yield('extra-styles')

        /* =========================================================
           RESPONSIVE
        ========================================================= */
        @media (max-width: 900px) {
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 2.5rem 1.75rem; }
        }
    </style>
    @yield('head-extra')
</head>
<body>

<div class="auth-left">
    <!-- Animated blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="left-content">
        <a href="/" class="left-logo">
            <i class="fa-solid fa-camera-retro"></i> LensStore
        </a>
        <h1 class="left-tagline">
            Mua sắm ống kính<br><span class="highlight">đỉnh cao & tin cậy</span>
        </h1>
        <p class="left-sub">Nền tảng mua bán ống kính máy ảnh chuyên nghiệp hàng đầu Việt Nam — bảo hành chính hãng, giao hàng siêu tốc.</p>
        <div class="left-features">
            <div class="feature-pill">
                <div class="pill-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div><strong>Bảo hành chính hãng</strong><small>Kích hoạt trực tuyến ngay sau mua</small></div>
            </div>
            <div class="feature-pill">
                <div class="pill-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <div><strong>Giao hàng siêu tốc</strong><small>Tích hợp GHN — theo dõi thời gian thực</small></div>
            </div>
            <div class="feature-pill">
                <div class="pill-icon"><i class="fa-solid fa-lock"></i></div>
                <div><strong>Bảo mật tuyệt đối</strong><small>Dữ liệu mã hóa AES-256 chuẩn ngân hàng</small></div>
            </div>
            <div class="feature-pill">
                <div class="pill-icon"><i class="fa-solid fa-headset"></i></div>
                <div><strong>Hỗ trợ 24/7</strong><small>Đội ngũ chuyên gia ống kính sẵn sàng</small></div>
            </div>
        </div>
    </div>
</div>

<!-- RIGHT -->
<div class="auth-right">
    @yield('content')
</div>

<script>
/**
 * Toggle hiển thị / ẩn mật khẩu
 * @param {string} inputId  - id của <input type="password">
 * @param {HTMLElement} btn - nút eye đã được click
 */
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
        btn.setAttribute('aria-label', 'Ẩn mật khẩu');
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
        btn.setAttribute('aria-label', 'Hiện mật khẩu');
    }
}
</script>
@yield('scripts')
</body>
</html>
