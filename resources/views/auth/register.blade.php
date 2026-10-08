<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - LensStore</title>
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
        .panel-features { display: flex; flex-direction: column; gap: 14px; text-align: left; width: 100%; max-width: 360px; }
        .panel-feature { display: flex; align-items: center; gap: 14px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 14px 18px; backdrop-filter: blur(10px); }
        .panel-feature i { color: var(--accent); font-size: 1.1rem; width: 20px; text-align: center; }
        .panel-feature strong { display: block; font-size: 0.9rem; }
        .panel-feature small { color: rgba(255,255,255,0.6); font-size: 0.8rem; }

        /* RIGHT PANEL */
        .right-panel { overflow-y: auto; padding: 3rem 2.5rem; display: flex; flex-direction: column; justify-content: center; }
        .form-header { margin-bottom: 2rem; }
        .form-header h2 { font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.4rem; }
        .form-header p { color: var(--text-muted); font-size: 0.9rem; }
        .form-header a { color: var(--primary); text-decoration: none; font-weight: 600; }

        /* Progress Bar */
        .progress-bar { display: flex; align-items: center; gap: 0; margin-bottom: 2.5rem; }
        .step-item { flex: 1; text-align: center; position: relative; }
        .step-item:not(:last-child)::after { content: ''; position: absolute; top: 18px; left: 50%; width: 100%; height: 2px; background: var(--border); z-index: 0; }
        .step-item.done:not(:last-child)::after { background: var(--primary); }
        .step-circle { width: 36px; height: 36px; border-radius: 50%; background: var(--surface); border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; margin: 0 auto 6px; position: relative; z-index: 1; transition: all 0.3s; }
        .step-item.active .step-circle { background: var(--primary); border-color: var(--primary); color: white; }
        .step-item.done .step-circle { background: var(--success); border-color: var(--success); color: white; }
        .step-label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); }
        .step-item.active .step-label { color: var(--primary); }

        /* Form Step */
        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeInUp 0.35s ease; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        /* Alerts */
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 10px; padding: 1rem 1.2rem; margin-bottom: 1.5rem; font-size: 0.9rem; display: flex; align-items: flex-start; gap: 10px; }
        .alert-danger ul { padding-left: 18px; margin-top: 4px; }

        /* Section Title */
        .section-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 1.25rem; padding-bottom: 8px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
        .section-label i { color: var(--primary); }

        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .form-grid .full { grid-column: 1 / -1; }
        .form-group { margin-bottom: 0; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 0.9rem; color: var(--text-main); }
        .form-group label .required { color: var(--danger); margin-left: 2px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem; pointer-events: none; }
        .form-control {
            width: 100%; padding: 0.75rem 1rem 0.75rem 2.8rem; font-size: 0.95rem; font-family: 'Inter';
            border: 1.5px solid var(--border); border-radius: 10px; background: var(--surface);
            color: var(--text-main); transition: all 0.2s; outline: none;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }
        .form-control.is-error { border-color: var(--danger); }
        select.form-control { appearance: none; cursor: pointer; }
        .help-text { font-size: 0.78rem; color: var(--text-muted); margin-top: 5px; }

        /* Password strength */
        .pw-strength { margin-top: 6px; display: flex; gap: 4px; }
        .pw-bar { height: 3px; flex: 1; background: var(--border); border-radius: 3px; transition: background 0.3s; }
        .pw-bar.weak { background: var(--danger); }
        .pw-bar.medium { background: var(--accent); }
        .pw-bar.strong { background: var(--success); }

        /* Security note */
        .security-note { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 1rem 1.2rem; margin: 1.5rem 0; display: flex; gap: 10px; font-size: 0.85rem; color: #166534; }
        .security-note i { margin-top: 2px; flex-shrink: 0; }

        /* Terms */
        .terms-check { display: flex; align-items: flex-start; gap: 10px; font-size: 0.87rem; color: var(--text-muted); }
        .terms-check input { margin-top: 3px; width: 16px; height: 16px; accent-color: var(--primary); flex-shrink: 0; cursor: pointer; }
        .terms-check a { color: var(--primary); text-decoration: none; font-weight: 600; }

        /* Buttons */
        .btn-primary {
            width: 100%; padding: 0.9rem; background: linear-gradient(135deg, var(--primary), #6366f1);
            color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem;
            font-family: 'Inter'; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79,70,229,0.35); }
        .btn-secondary { width: 100%; padding: 0.9rem; background: transparent; color: var(--text-main); border: 1.5px solid var(--border); border-radius: 12px; font-weight: 600; font-size: 0.95rem; font-family: 'Inter'; cursor: pointer; transition: all 0.2s; }
        .btn-secondary:hover { background: var(--surface2); }
        .btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 1.5rem; }

        .login-link { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted); }
        .login-link a { color: var(--primary); font-weight: 600; text-decoration: none; }

        @media (max-width: 900px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 2rem 1.5rem; }
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
        <h2 class="panel-title">Trải nghiệm mua sắm<br><span>cao cấp hơn</span></h2>
        <p class="panel-desc">Đăng ký để được bảo vệ quyền lợi tối đa khi sở hữu những chiếc ống kính giá trị cao.</p>
        <div class="panel-features">
            <div class="panel-feature">
                <i class="fa-solid fa-shield-halved"></i>
                <div><strong>Bảo hành chính hãng</strong><small>Kích hoạt trực tuyến ngay sau mua</small></div>
            </div>
            <div class="panel-feature">
                <i class="fa-solid fa-file-invoice"></i>
                <div><strong>Hóa đơn VAT đầy đủ</strong><small>Xuất hóa đơn theo yêu cầu</small></div>
            </div>
            <div class="panel-feature">
                <i class="fa-solid fa-lock"></i>
                <div><strong>Bảo mật tuyệt đối</strong><small>Thông tin được mã hóa AES-256</small></div>
            </div>
            <div class="panel-feature">
                <i class="fa-solid fa-user-shield"></i>
                <div><strong>Xác thực danh tính</strong><small>Bảo vệ chống gian lận, giả mạo</small></div>
            </div>
        </div>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="form-header">
        <h2>Tạo tài khoản LensStore</h2>
        <p>Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a></p>
    </div>

    <!-- Progress Steps -->
    <div class="progress-bar">
        <div class="step-item active" id="step-indicator-1">
            <div class="step-circle">1</div>
            <div class="step-label">Cá nhân</div>
        </div>
        <div class="step-item" id="step-indicator-2">
            <div class="step-circle">2</div>
            <div class="step-label">Pháp lý</div>
        </div>
        <div class="step-item" id="step-indicator-3">
            <div class="step-circle">3</div>
            <div class="step-label">Tài khoản</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert-danger">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Vui lòng kiểm tra lại:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" id="registerForm">
        @csrf

        <!-- STEP 1: Thông tin cá nhân -->
        <div class="form-step active" id="step-1">
            <div class="section-label"><i class="fa-solid fa-user"></i> Thông tin cá nhân</div>
            <div class="form-grid">
                <div class="form-group full">
                    <label>Họ và tên đầy đủ <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-error' : '' }}" placeholder="Nguyễn Văn An" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ngày sinh <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-cake-candles"></i>
                        <input type="date" name="birthday" class="form-control {{ $errors->has('birthday') ? 'is-error' : '' }}" value="{{ old('birthday') }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}" required>
                    </div>
                    <div class="help-text">Phải đủ 18 tuổi trở lên</div>
                </div>

                <div class="form-group">
                    <label>Giới tính <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-venus-mars"></i>
                        <select name="gender" class="form-control {{ $errors->has('gender') ? 'is-error' : '' }}" required>
                            <option value="">-- Chọn giới tính --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Số điện thoại <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" name="phone" class="form-control {{ $errors->has('phone') ? 'is-error' : '' }}" placeholder="0901 234 567" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email liên hệ <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-error' : '' }}" placeholder="email@example.com" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group full">
                    <label>Địa chỉ thường trú <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-map-pin"></i>
                        <input type="text" name="address" class="form-control {{ $errors->has('address') ? 'is-error' : '' }}" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" value="{{ old('address') }}" required>
                    </div>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="button" class="btn-primary" onclick="nextStep(1)">
                    Tiếp theo <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- STEP 2: Thông tin pháp lý -->
        <div class="form-step" id="step-2">
            <div class="section-label"><i class="fa-solid fa-id-card"></i> Xác thực danh tính (CCCD/CMND)</div>

            <div class="security-note">
                <i class="fa-solid fa-lock"></i>
                <div>Thông tin CCCD/CMND được thu thập để <strong>xác minh danh tính và bảo vệ quyền lợi</strong> của bạn khi mua sản phẩm có giá trị cao. Tất cả dữ liệu được mã hóa và tuân thủ quy định pháp luật về bảo vệ dữ liệu cá nhân.</div>
            </div>

            <div class="form-grid">
                <div class="form-group full">
                    <label>Số CCCD / CMND <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-id-card"></i>
                        <input type="text" name="cccd" class="form-control {{ $errors->has('cccd') ? 'is-error' : '' }}" placeholder="001234567890 (12 chữ số)" value="{{ old('cccd') }}" maxlength="20" required>
                    </div>
                    <div class="help-text"><i class="fa-solid fa-circle-info"></i> CCCD 12 số hoặc CMND 9 số</div>
                </div>

                <div class="form-group full" style="margin-top: 1rem;">
                    <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">
                        <i class="fa-solid fa-triangle-exclamation" style="color: var(--accent);"></i>
                        Tại sao chúng tôi cần thông tin này?
                    </label>
                    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.2rem; font-size: 0.85rem; color: var(--text-muted); line-height: 1.8;">
                        <p>✓ <strong>Bảo vệ quyền lợi khách hàng</strong>: Đảm bảo chỉ chủ sở hữu thật mới có thể yêu cầu bảo hành</p>
                        <p>✓ <strong>Phòng chống gian lận</strong>: Ngăn chặn tình trạng giả mạo danh tính khi mua sản phẩm cao cấp</p>
                        <p>✓ <strong>Xuất hóa đơn VAT</strong>: Phục vụ việc xuất hóa đơn theo đúng quy định pháp luật</p>
                    </div>
                </div>
            </div>

            <div class="btn-row">
                <button type="button" class="btn-secondary" onclick="prevStep(2)">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </button>
                <button type="button" class="btn-primary" onclick="nextStep(2)">
                    Tiếp theo <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- STEP 3: Tạo mật khẩu -->
        <div class="form-step" id="step-3">
            <div class="section-label"><i class="fa-solid fa-key"></i> Thiết lập mật khẩu</div>

            <div class="form-grid">
                <div class="form-group full">
                    <label>Mật khẩu <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;"></i>
                        <input type="password" name="password" id="password"
                            class="form-control {{ $errors->has('password') ? 'is-error' : '' }}"
                            placeholder="Tối thiểu 6 ký tự"
                            oninput="checkPwStrength(this.value)" required>
                        <button type="button" class="eye-toggle" onclick="togglePassword('password', this)" aria-label="Hiện mật khẩu">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-bar" id="bar1"></div>
                        <div class="pw-bar" id="bar2"></div>
                        <div class="pw-bar" id="bar3"></div>
                        <div class="pw-bar" id="bar4"></div>
                    </div>
                    <div class="help-text" id="pw-hint">Mật khẩu mạnh nhất khi kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</div>
                </div>

                <div class="form-group full">
                    <label>Xác nhận mật khẩu <span class="required">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock-open input-icon" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control"
                            placeholder="Nhập lại mật khẩu" required>
                        <button type="button" class="eye-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="Hiện mật khẩu">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div style="margin: 1.5rem 0;">
                <div class="terms-check">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">Tôi đã đọc và đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách quyền riêng tư</a> của LensStore, bao gồm việc thu thập và xử lý thông tin CCCD/CMND để xác minh danh tính.</label>
                </div>
            </div>

            <div class="btn-row">
                <button type="button" class="btn-secondary" onclick="prevStep(3)">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Hoàn tất đăng ký
                </button>
            </div>
        </div>
    </form>

    <div class="login-link">
        Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a>
    </div>
</div>

<script>
let currentStep = 1;

function nextStep(from) {
    // Basic validation for step 1
    if (from === 1) {
        const required = document.querySelectorAll('#step-' + from + ' [required]');
        let valid = true;
        required.forEach(el => {
            if (!el.value.trim()) { el.classList.add('is-error'); valid = false; }
            else el.classList.remove('is-error');
        });
        if (!valid) return;
    }
    // CCCD validation step 2
    if (from === 2) {
        const cccd = document.querySelector('[name="cccd"]');
        const val = cccd.value.trim();
        if (val.length < 9) { cccd.classList.add('is-error'); alert('Số CCCD/CMND phải có ít nhất 9 ký tự.'); return; }
        cccd.classList.remove('is-error');
    }
    setStep(from + 1);
}

function prevStep(from) {
    setStep(from - 1);
}

function setStep(n) {
    document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
    document.getElementById('step-' + n).classList.add('active');
    // Update indicators
    for (let i = 1; i <= 3; i++) {
        const indicator = document.getElementById('step-indicator-' + i);
        const circle = indicator.querySelector('.step-circle');
        indicator.classList.remove('active', 'done');
        if (i < n) { indicator.classList.add('done'); circle.innerHTML = '<i class="fa-solid fa-check"></i>'; }
        else if (i === n) { indicator.classList.add('active'); circle.innerHTML = i; }
        else { circle.innerHTML = i; }
    }
    currentStep = n;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function checkPwStrength(val) {
    const bars = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3'), document.getElementById('bar4')];
    const hint = document.getElementById('pw-hint');
    bars.forEach(b => b.className = 'pw-bar');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const cls = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
    const labels = { weak: 'Yếu — cần thêm số, ký tự đặc biệt', medium: 'Trung bình — hãy thêm ký tự đặc biệt', strong: 'Mạnh — tuyệt vời!' };
    for (let i = 0; i < score; i++) bars[i].classList.add(cls);
    hint.textContent = val ? labels[cls] || '' : 'Mật khẩu mạnh nhất khi kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt';
}

// If validation errors exist, jump to last step
@if ($errors->any())
    setStep(3);
@endif
</script>

</body>
</html>
