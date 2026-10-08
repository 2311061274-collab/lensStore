@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu – LensStore')

@section('content')

<span class="form-badge"><i class="fa-solid fa-lock"></i> Bảo mật</span>
<h2 class="form-title">Tạo mật khẩu mới</h2>
<p class="form-subtitle">Vui lòng nhập mật khẩu mới cho tài khoản của bạn.</p>

@if ($errors->any())
    <div class="alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="email" value="{{ $email }}">

    <div class="form-group">
        <label for="password">Mật khẩu mới <span class="req">*</span></label>
        <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
            <input
                type="password" id="password" name="password"
                class="form-control {{ $errors->has('password') ? 'is-error' : '' }}"
                placeholder="Tối thiểu 6 ký tự"
                required autofocus
            >
            <button type="button" class="eye-toggle" onclick="togglePassword('password', this)" aria-label="Hiện mật khẩu">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Xác nhận mật khẩu <span class="req">*</span></label>
        <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
            <input
                type="password" id="password_confirmation" name="password_confirmation"
                class="form-control"
                placeholder="Nhập lại mật khẩu"
                required
            >
            <button type="button" class="eye-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="Hiện mật khẩu">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn-primary">
        Đặt lại mật khẩu
    </button>
</form>

@endsection
