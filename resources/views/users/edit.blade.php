@extends('layouts.admin')

@section('title', 'Sửa Tài Khoản')

@section('content')
<div class="page-header">
    <h1>Cập nhật Tài Khoản</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group" style="text-align: center; margin-bottom: 2rem;">
            <div style="margin-bottom: 15px; position: relative; display: inline-block;">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #6366f1); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <label for="avatar" style="position: absolute; bottom: 0; right: 0; background: #fff; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border: 1px solid var(--line);">
                    <i class="fa-solid fa-camera" style="color: var(--primary);"></i>
                </label>
            </div>
            <input type="file" name="avatar" id="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
        </div>
        
        <div class="form-group">
            <label for="name">Họ và tên</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu mới (Để trống nếu không muốn đổi)</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Xác nhận mật khẩu mới</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>

        <div class="form-group">
            <label for="role">Chức vụ / Vai trò</label>
            @php $currentRole = $user->roles->first()?->name ?? $user->role; @endphp
            <select name="role" id="role" class="form-control" required>
                @foreach($roles as $r)
                <option value="{{ $r->name }}" {{ old('role', $currentRole) == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
            <small class="muted" style="display: block; margin-top: .4rem;">Thay đổi chức vụ tại đây sẽ cập nhật quyền truy cập của tài khoản này.</small>
        </div>

        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <i class="fa-solid fa-save"></i> Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = input.parentElement.querySelector('img');
            const placeholder = input.parentElement.querySelector('div > div');
            if(img) {
                img.src = e.target.result;
            } else if(placeholder) {
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.style.cssText = "width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.1);";
                placeholder.replaceWith(newImg);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
