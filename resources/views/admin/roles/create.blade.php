@extends('layouts.admin')

@section('title', 'Thêm chức vụ')
@section('subtitle', 'Đặt tên chức vụ và chọn quyền theo nhóm nghiệp vụ')

@section('actions')
<a href="{{ route('admin.roles.index') }}" class="btn btn-outline btn-sm">
    <i class="fa-solid fa-arrow-left"></i> Danh sách
</a>
@endsection

@section('content')
<form action="{{ route('admin.roles.store') }}" method="POST" class="role-shell">
    @csrf

    <div class="role-identity">
        <div class="form-group">
            <label for="name">Tên chức vụ</label>
            <input type="text" name="name" id="name" class="form-control" style="width:100%;max-width:420px;font-size:1rem;padding:.7rem .9rem;"
                   placeholder="VD: Nhân viên kho, Kế toán, CSKH..."
                   value="{{ old('name') }}" required autofocus>
            <small class="muted" style="display:block;margin-top:.45rem;">
                Tên này sẽ hiện khi gán chức vụ cho tài khoản nhân sự.
            </small>
        </div>
    </div>

    @include('admin.roles._permissions', [
        'permissions' => $permissions,
        'selected' => old('permissions', []),
    ])

    <div class="role-actions">
        <div class="muted" style="font-size:.82rem;">
            <i class="fa-solid fa-lightbulb"></i>
            Tip: dùng “Chọn nhóm này” để cấp nhanh quyền theo module.
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">Hủy</a>
            <button type="submit" class="btn">
                <i class="fa-solid fa-floppy-disk"></i> Lưu chức vụ
            </button>
        </div>
    </div>
</form>
@endsection
