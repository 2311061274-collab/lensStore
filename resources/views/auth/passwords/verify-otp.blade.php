@extends('layouts.auth')

@section('title', 'Xác thực mã – LensStore')

@section('content')

<span class="form-badge"><i class="fa-solid fa-shield-halved"></i> Xác thực tài khoản</span>
<h2 class="form-title">Nhập mã xác thực</h2>
<p class="form-subtitle">Mã xác thực đã được gửi đến email <strong>{{ session('reset_password_email') }}</strong></p>

@if ($errors->any())
    <div class="alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
@endif

@if (session('success'))
    <div style="background:#f0fdf4;color:#166534;border:1px solid #86efac;border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.5rem;font-size:.88rem;display:flex;gap:9px;align-items:center;">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('password.verify') }}">
    @csrf

    <div class="form-group">
        <label for="otp">Mã xác thực (6 số) <span class="req">*</span></label>
        <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-key"></i></span>
            <input
                type="text" id="otp" name="otp"
                class="form-control no-eye {{ $errors->has('otp') ? 'is-error' : '' }}"
                placeholder="Ví dụ: 123456"
                maxlength="6"
                required autofocus
                style="letter-spacing: 5px; font-weight: 600; text-align: center;"
            >
        </div>
    </div>

    <button type="submit" class="btn-primary">
        Xác minh
    </button>
</form>

<form method="POST" action="{{ route('password.email') }}" style="margin-top: 1.5rem; text-align: center;">
    @csrf
    <input type="hidden" name="email" value="{{ session('reset_password_email') }}">
    <p style="font-size: 0.9rem; color: var(--text-muted);">
        Chưa nhận được mã? 
        <button type="submit" style="background: none; border: none; color: var(--primary-light); font-weight: 600; cursor: pointer; padding: 0;">Gửi lại</button>
    </p>
</form>

@endsection
