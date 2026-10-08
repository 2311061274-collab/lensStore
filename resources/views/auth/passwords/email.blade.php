@extends('layouts.auth')

@section('title', 'Quên mật khẩu – LensStore')

@section('content')

<span class="form-badge"><i class="fa-solid fa-envelope-circle-check"></i> Khôi phục mật khẩu</span>
<h2 class="form-title">Quên mật khẩu?</h2>
<p class="form-subtitle">Nhập email của bạn, chúng tôi sẽ gửi mã xác thực để khôi phục mật khẩu.</p>

@if ($errors->any())
    <div class="alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
        <label for="email">Email <span class="req">*</span></label>
        <div class="input-wrap">
            <span class="input-icon"><i class="fa-solid fa-envelope"></i></span>
            <input
                type="email" id="email" name="email"
                class="form-control no-eye {{ $errors->has('email') ? 'is-error' : '' }}"
                value="{{ old('email') }}"
                placeholder="email@example.com"
                required autofocus
            >
        </div>
    </div>

    <button type="submit" class="btn-primary">
        Gửi mã xác thực
    </button>
</form>

<div class="auth-footer" style="margin-top:1.8rem;">
    <a href="{{ route('login') }}"><i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập</a>
</div>

@endsection
