@extends('layouts.admin')

@section('title', 'Chi tiết Tài Khoản')

@section('content')
<div class="page-header">
    <h1>Chi tiết Tài Khoản</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; gap: 2rem; align-items: flex-start;">
        <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--canvas); display: grid; place-items: center; font-size: 3rem; color: var(--muted); border: 2px solid var(--line);">
            <i class="fa-solid fa-user"></i>
        </div>
        <div style="flex: 1;">
            <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--ink);">{{ $user->name }}</h2>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; color: var(--ink-soft);">
                <p><strong><i class="fa-solid fa-envelope"></i> Email:</strong> {{ $user->email }}</p>
                <p>
                    <strong><i class="fa-solid fa-shield-halved"></i> Vai trò:</strong>
                    @if($user->role === 'admin')
                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Quản trị viên</span>
                    @elseif($user->role === 'staff')
                        <span style="background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Nhân viên</span>
                    @else
                        <span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Khách hàng</span>
                    @endif
                </p>
                <p><strong><i class="fa-solid fa-calendar-days"></i> Ngày tham gia:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pen"></i> Chỉnh sửa
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản này?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash"></i> Xóa tài khoản
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
