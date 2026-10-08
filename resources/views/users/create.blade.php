@extends('layouts.admin')

@section('title', 'Thêm Tài Khoản')

@section('content')
<div class="page-header">
    <h1>Thêm Tài Khoản Mới</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">Họ và tên</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="role">Chức vụ / Vai trò</label>
            <select name="role" id="role" class="form-control" required>
                <option value="" disabled selected>-- Chọn chức vụ --</option>
                @foreach($roles as $r)
                <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
            <small class="muted" style="display:block; margin-top: .4rem;">
                <i class="fa-solid fa-circle-info"></i> Chức vụ quyết định các mục trong trang Admin mà tài khoản này được phép truy cập.
            </small>
        </div>

        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-save"></i> Lưu Người Dùng
            </button>
        </div>
    </form>
</div>
@endsection
