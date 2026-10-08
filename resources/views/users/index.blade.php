@extends('layouts.admin')

@section('title', 'Quản lý Tài Khoản')

@section('content')
<div class="page-header">
    <h1>Quản Lý Tài Khoản</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm Tài Khoản
    </a>
</div>

{{-- Filter Tabs by Role --}}
<div class="status-tabs" style="margin-bottom: 1.1rem;">
    <a href="{{ route('admin.users.index') }}" class="{{ !request('role') ? 'is-active' : '' }}">
        <i class="fa-solid fa-users"></i> Tất cả
        <span style="background: rgba(255,255,255,.15); border-radius: 20px; padding: 1px 7px; font-size: .72rem; margin-left: 4px;">{{ \App\Models\User::count() }}</span>
    </a>
    @foreach($roles as $r)
    <a href="{{ route('admin.users.index', ['role' => $r->name]) }}" class="{{ request('role') == $r->name ? 'is-active' : '' }}">
        {{ $r->name }}
        <span style="background: rgba(255,255,255,.15); border-radius: 20px; padding: 1px 7px; font-size: .72rem; margin-left: 4px;">{{ \App\Models\User::whereHas('roles', fn($q) => $q->where('name', $r->name))->count() }}</span>
    </a>
    @endforeach
</div>

<div class="card flush">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Tài khoản</th>
                    <th>Email</th>
                    <th>Chức vụ / Vai trò</th>
                    <th>Ngày tạo</th>
                    <th style="text-align: right; width: 140px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                @php $currentRole = $user->roles->first()?->name ?? ''; @endphp
                <tr>
                    <td class="muted" style="font-size: .8rem;">#{{ $user->id }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: .75rem;">
                            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--canvas); display: grid; place-items: center; font-weight: 700; font-size: .9rem; color: var(--accent); border: 1px solid var(--line); flex-shrink: 0; overflow: hidden;">
                                @if($user->avatar_url)
                                    <a href="{{ $user->avatar_url }}" target="_blank" style="width: 100%; height: 100%; display: block;" title="Xem ảnh">
                                        <img src="{{ $user->avatar_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                @else
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div style="font-weight: 600; color: var(--ink);">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td class="muted" style="font-size: .875rem;">{{ $user->email }}</td>
                    <td>
                        <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="role" onchange="this.form.submit()" style="padding: .3rem .65rem; border-radius: 8px; border: 1px solid; font-size: .8rem; font-weight: 600; cursor: pointer;
                                {{ $currentRole === 'admin' ? 'border-color: #fca5a5; background: #fff1f2; color: #991b1b;' : '' }}
                                {{ $currentRole === 'staff' ? 'border-color: #fcd34d; background: #fffbeb; color: #b45309;' : '' }}
                                {{ $currentRole === 'customer' ? 'border-color: #93c5fd; background: #eff6ff; color: #1d4ed8;' : '' }}
                                {{ (!in_array($currentRole, ['admin','staff','customer']) && $currentRole !== '') ? 'border-color: #a5f3a5; background: #f0fff0; color: #166534;' : '' }}
                            ">
                                <option value="" disabled {{ $currentRole === '' ? 'selected' : '' }}>-- Chọn chức vụ --</option>
                                @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ $currentRole === $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="muted" style="font-size: .8rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="actions" style="justify-content: flex-end;">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline btn-sm" title="Xem chi tiết">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm" title="Sửa thông tin">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if($user->avatar_url)
                            <form action="{{ route('admin.users.remove-avatar', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ảnh đại diện của người dùng này và yêu cầu họ cập nhật ảnh mới?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm" style="color: var(--warn); border-color: var(--warn-soft); background: var(--warn-soft);" title="Xóa ảnh đại diện">
                                    <i class="fa-solid fa-image-portrait"></i><i class="fa-solid fa-xmark" style="font-size: 0.6em; margin-left: -2px;"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa tài khoản này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fa-solid fa-users-slash"></i>
                            <p>Không tìm thấy tài khoản nào</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $users->links('vendor.pagination.admin') }}
</div>
@endsection
