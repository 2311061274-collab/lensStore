@extends('layouts.auth')

@section('title', 'Đăng nhập – LensStore')

@section('content')

<span class="form-badge"><i class="fa-solid fa-circle-user"></i> Chào mừng trở lại</span>
<h2 class="form-title">Đăng nhập</h2>
<p class="form-subtitle">Chưa có tài khoản? <a href="{{ route('register') }}">Tạo tài khoản miễn phí</a></p>

@if ($errors->any())
    <div class="alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <strong>Đăng nhập thất bại:</strong>
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
@endif

@if(session('success'))
    <div style="background:#f0fdf4;color:#166534;border:1px solid #86efac;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.5rem;font-size:.88rem;display:flex;gap:9px;align-items:center;">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('login.post') }}" autocomplete="on">
    @csrf

    {{-- Email --}}
    <div class="form-group">
        <label for="email">Email <span class="req">*</span></label>
        <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-envelope"></i></span>
            <input
                type="email" id="email" name="email"
                class="form-control no-eye {{ $errors->has('email') ? 'is-error' : '' }}"
                value="{{ old('email') }}"
                placeholder="email@example.com"
                required autofocus autocomplete="email"
            >
        </div>
    </div>

    {{-- Password + eye --}}
    <div class="form-group">
        <label for="password">Mật khẩu <span class="req">*</span></label>
        <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
            <input
                type="password" id="password" name="password"
                class="form-control {{ $errors->has('password') ? 'is-error' : '' }}"
                placeholder="••••••••"
                required autocomplete="current-password"
            >
            <button type="button" class="eye-toggle" onclick="togglePassword('password', this)" aria-label="Hiện mật khẩu">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
    </div>

    {{-- Remember + forgot --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.6rem;">
        <label style="display:flex;align-items:center;gap:8px;font-size:.875rem;color:var(--text-muted);cursor:pointer;font-weight:500;">
            <input type="checkbox" name="remember" style="width:15px;height:15px;accent-color:var(--primary-light);cursor:pointer;">
            Ghi nhớ đăng nhập
        </label>
        <a href="{{ route('password.request') }}" style="font-size:.875rem;color:var(--primary-light);font-weight:600;text-decoration:none;">Quên mật khẩu?</a>
    </div>

    <button type="submit" class="btn-primary">
        <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
    </button>

    {{-- Chọn nhanh tài khoản kiểm thử Demo --}}
    <div style="margin-top:1.25rem;padding:12px 14px;background:rgba(255,255,255,0.06);border:1px dashed rgba(255,255,255,0.2);border-radius:12px;font-size:0.83rem;color:#cbd5e1;">
        <div style="font-weight:600;margin-bottom:8px;color:#f59e0b;display:flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-bolt"></i> Chọn nhanh tài khoản kiểm thử:
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button type="button" onclick="fillLoginCredentials('admin@example.com','password')" style="background:#4f46e5;color:white;border:none;padding:7px 12px;border-radius:8px;cursor:pointer;font-size:0.8rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-shield-halved"></i> 👑 Admin (Vào Dashboard)
            </button>
            <button type="button" onclick="fillLoginCredentials('user@example.com','password')" style="background:#0d9488;color:white;border:none;padding:7px 12px;border-radius:8px;cursor:pointer;font-size:0.8rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-user"></i> 👤 Khách hàng (Vào Trang User)
            </button>
        </div>
    </div>

    <script>
    function fillLoginCredentials(email, pwd) {
        var emailInput = document.getElementById('email');
        var pwdInput = document.getElementById('password');
        if (emailInput) emailInput.value = email;
        if (pwdInput) pwdInput.value = pwd;
    }
    </script>
</form>

<div class="auth-footer" style="margin-top:1.8rem;">
    Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
</div>


@endsection
