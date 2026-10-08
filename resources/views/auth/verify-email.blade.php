<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email - LensStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5; --primary-hover: #4338ca; --accent: #f59e0b;
            --dark: #0f172a; --surface: #ffffff; --surface2: #f8fafc;
            --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0;
            --danger: #ef4444; --success: #10b981;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: var(--surface2);
            -webkit-font-smoothing: antialiased;
        }

        /* LEFT PANEL */
        .left-panel {
            background: linear-gradient(150deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 4rem 3rem; text-align: center; color: white; position: relative; overflow: hidden;
        }
        .left-panel::before {
            content: ''; position: absolute; inset: 0;
            background: url('https://images.unsplash.com/photo-1606986628253-06ac7e7e6cf5?w=800&auto=format&fit=crop&q=60') center/cover;
            opacity: 0.1;
        }
        .left-content { position: relative; z-index: 1; }
        .brand-logo { font-size: 2rem; font-weight: 800; color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 3rem; }
        .brand-logo .icon { width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); }
        .panel-title { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; line-height: 1.3; margin-bottom: 1rem; }
        .panel-title span { color: var(--accent); }
        .panel-desc { color: rgba(255,255,255,0.7); line-height: 1.8; font-size: 0.95rem; margin-bottom: 3rem; }

        .otp-illustration {
            width: 140px; height: 140px; background: rgba(255,255,255,0.08);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 2rem; border: 2px solid rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
        }
        .otp-illustration i { font-size: 4rem; color: var(--accent); }

        /* RIGHT PANEL */
        .right-panel {
            display: flex; flex-direction: column; justify-content: center;
            padding: 3rem 3.5rem; max-width: 520px; margin: 0 auto; width: 100%;
        }

        .form-header { text-align: center; margin-bottom: 2rem; }
        .form-header .icon-wrap {
            width: 72px; height: 72px; background: linear-gradient(135deg, #ede9fe, #ddd6fe);
            border-radius: 20px; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .form-header .icon-wrap i { font-size: 2rem; color: var(--primary); }
        .form-header h2 { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.5rem; color: var(--text-main); }
        .form-header p { color: var(--text-muted); font-size: 0.92rem; line-height: 1.6; }
        .form-header .email-badge {
            display: inline-block; background: #ede9fe; color: var(--primary); font-weight: 600;
            font-size: 0.88rem; padding: 4px 12px; border-radius: 20px; margin-top: 8px;
        }

        /* OTP Input Group */
        .otp-group { display: flex; gap: 10px; justify-content: center; margin: 2rem 0; }
        .otp-input {
            width: 56px; height: 64px; font-size: 1.6rem; font-weight: 700; font-family: 'Inter';
            text-align: center; border: 2px solid var(--border); border-radius: 12px;
            background: var(--surface); color: var(--text-main); outline: none;
            transition: all 0.2s; caret-color: transparent;
        }
        .otp-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,70,229,0.12); transform: translateY(-2px); }
        .otp-input.filled { border-color: var(--primary); background: #ede9fe; color: var(--primary); }
        .otp-input.error { border-color: var(--danger); background: #fee2e2; }

        /* Hidden real input */
        #otp-real { display: none; }

        /* Alerts */
        .alert { border-radius: 10px; padding: 0.9rem 1.2rem; margin-bottom: 1.5rem; font-size: 0.9rem; display: flex; align-items: flex-start; gap: 10px; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-info { background: #ede9fe; color: #4338ca; border: 1px solid #c4b5fd; }

        /* Timer */
        .timer-wrap { text-align: center; margin-bottom: 1.5rem; color: var(--text-muted); font-size: 0.9rem; }
        .timer-wrap #countdown { font-weight: 700; color: var(--primary); }
        .timer-wrap #countdown.expired { color: var(--danger); }

        /* Buttons */
        .btn-primary {
            width: 100%; padding: 0.95rem; background: linear-gradient(135deg, var(--primary), #6366f1);
            color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem;
            font-family: 'Inter'; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79,70,229,0.35); }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

        .resend-section { text-align: center; margin-top: 1.5rem; }
        .resend-section p { font-size: 0.88rem; color: var(--text-muted); margin-bottom: 0.75rem; }

        .btn-resend {
            background: none; border: 1.5px solid var(--border); border-radius: 10px;
            padding: 0.6rem 1.5rem; font-family: 'Inter'; font-size: 0.88rem; font-weight: 600;
            color: var(--primary); cursor: pointer; transition: all 0.2s;
        }
        .btn-resend:hover:not(:disabled) { background: #ede9fe; border-color: var(--primary); }
        .btn-resend:disabled { opacity: 0.4; cursor: not-allowed; }

        .back-link { text-align: center; margin-top: 1.25rem; font-size: 0.88rem; color: var(--text-muted); }
        .back-link a { color: var(--primary); font-weight: 600; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }

        @media (max-width: 900px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 2rem 1.5rem; max-width: 100%; }
        }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div class="left-content">
        <a href="/" class="brand-logo">
            <div class="icon"><i class="fa-solid fa-camera-retro"></i></div>
            LensStore
        </a>
        <div class="otp-illustration">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h2 class="panel-title">Bảo mật tài khoản<br><span>của bạn</span></h2>
        <p class="panel-desc">Mã xác thực 6 số được gửi về email giúp đảm bảo chỉ bạn mới có thể kích hoạt tài khoản.</p>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="form-header">
        <div class="icon-wrap"><i class="fa-solid fa-envelope-open-text"></i></div>
        <h2>Nhập mã xác thực</h2>
        <p>Mã gồm 6 chữ số đã được gửi đến email của bạn</p>
        <span class="email-badge"><i class="fa-solid fa-envelope" style="margin-right:6px;"></i>{{ session('otp_email') ?? (Auth::check() ? Auth::user()->email : '') }}</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('message') }}</div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- OTP Form -->
    <form method="POST" action="{{ route('verification.verify-otp') }}" id="otpForm">
        @csrf
        <input type="hidden" name="otp" id="otp-real">

        <div class="otp-group" id="otp-group">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
            <input class="otp-input" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
        </div>

        <div class="timer-wrap">
            Mã hết hạn sau: <span id="countdown">15:00</span>
        </div>

        <button type="submit" class="btn-primary" id="submitBtn" disabled>
            <i class="fa-solid fa-check-circle"></i> Xác nhận mã
        </button>
    </form>

    <!-- Resend Form -->
    <div class="resend-section">
        <p>Chưa nhận được mã?</p>
        <form method="POST" action="{{ route('verification.send') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn-resend" id="resendBtn">
                <i class="fa-solid fa-rotate-right"></i> Gửi lại mã
            </button>
        </form>
    </div>

    <div class="back-link">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-arrow-left"></i> Đăng xuất
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
    </div>
</div>

<script>
const inputs = document.querySelectorAll('.otp-input');
const realInput = document.getElementById('otp-real');
const submitBtn = document.getElementById('submitBtn');

// Focus first input
inputs[0].focus();

inputs.forEach((input, index) => {
    input.addEventListener('input', function(e) {
        // Only digits
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value) {
            this.classList.add('filled');
            if (index < inputs.length - 1) inputs[index + 1].focus();
        } else {
            this.classList.remove('filled');
        }
        syncOtp();
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && index > 0) {
            inputs[index - 1].focus();
            inputs[index - 1].value = '';
            inputs[index - 1].classList.remove('filled');
            syncOtp();
        }
    });

    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
        pasted.split('').forEach((char, i) => {
            if (inputs[i]) {
                inputs[i].value = char;
                inputs[i].classList.add('filled');
            }
        });
        syncOtp();
        const nextEmpty = [...inputs].findIndex(inp => !inp.value);
        if (nextEmpty !== -1) inputs[nextEmpty].focus();
        else inputs[inputs.length - 1].focus();
    });
});

function syncOtp() {
    const code = [...inputs].map(i => i.value).join('');
    realInput.value = code;
    submitBtn.disabled = code.length !== 6;
}

// Countdown timer (15 minutes)
let seconds = 15 * 60;
const countdownEl = document.getElementById('countdown');
const timer = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
        clearInterval(timer);
        countdownEl.textContent = 'Đã hết hạn';
        countdownEl.classList.add('expired');
        submitBtn.disabled = true;
        return;
    }
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    countdownEl.textContent = m + ':' + s;
}, 1000);

// Mark error inputs if there's a validation error
@if ($errors->any())
    inputs.forEach(inp => inp.classList.add('error'));
@endif
</script>

</body>
</html>
