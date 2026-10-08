@extends('layouts.admin')

@section('title', 'Sửa chức vụ')
@section('subtitle', 'Cập nhật quyền cho: ' . $role->name)

@section('actions')
<a href="{{ route('admin.roles.index') }}" class="btn btn-outline btn-sm">
    <i class="fa-solid fa-arrow-left"></i> Danh sách
</a>
@endsection

@section('content')
<form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="role-shell">
    @csrf
    @method('PUT')

    <div class="role-identity">
        <div class="form-group">
            <label for="name">Tên chức vụ</label>
            <input type="text" name="name" id="name" class="form-control" style="width:100%;max-width:420px;font-size:1rem;padding:.7rem .9rem;"
                   value="{{ old('name', $role->name) }}"
                   {{ $role->name === 'admin' ? 'readonly' : '' }}
                   required>
            @if($role->name === 'admin')
                <small class="muted" style="display:block;margin-top:.45rem;">
                    <i class="fa-solid fa-lock"></i> Chức vụ admin gốc — không đổi tên được.
                </small>
            @endif
        </div>
    </div>

    @include('admin.roles._permissions', [
        'permissions' => $permissions,
        'selected' => old('permissions', $rolePermissions),
    ])

    <div class="role-actions">
        <div class="muted" style="font-size:.82rem;">
            Thay đổi quyền sẽ áp dụng ngay cho mọi tài khoản đang mang chức vụ này.
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline">Hủy</a>
            <button type="submit" class="btn">
                <i class="fa-solid fa-floppy-disk"></i> Cập nhật chức vụ
            </button>
        </div>
    </div>
</form>
@endsection
