@extends('layouts.admin')

@section('title', 'Phân quyền')
@section('subtitle', 'Chức vụ & quyền truy cập theo module')

@section('actions')
<a href="{{ route('admin.roles.create') }}" class="btn">
    <i class="fa-solid fa-plus"></i> Thêm chức vụ
</a>
@endsection

@section('content')
@php use App\Support\PermissionCatalog; @endphp

<div class="card flush" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th width="56">ID</th>
                    <th width="200">Chức vụ</th>
                    <th>Quyền theo nhóm</th>
                    <th style="text-align:right;width:140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($roles as $role)
                @php
                    $owned = $role->permissions->pluck('name')->all();
                @endphp
                <tr>
                    <td class="muted">#{{ $role->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.65rem;">
                            <div style="width:36px;height:36px;border-radius:10px;background:var(--accent-soft);display:grid;place-items:center;color:var(--accent);flex-shrink:0;">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <strong>{{ $role->name }}</strong>
                                <div class="muted" style="font-size:.72rem;margin-top:.1rem;">
                                    {{ count($owned) }} quyền
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if(empty($owned))
                            <span class="muted" style="font-size:.82rem;font-style:italic;">Chưa cấp quyền</span>
                        @else
                            <div style="display:flex;flex-direction:column;gap:.55rem;">
                                @foreach(PermissionCatalog::groups() as $group)
                                    @php
                                        $keys = array_values(array_intersect($group['perms'], $owned));
                                    @endphp
                                    @continue(empty($keys))
                                    <div>
                                        <div style="font-size:.68rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">
                                            <i class="fa-solid {{ $group['icon'] }}"></i> {{ $group['title'] }}
                                        </div>
                                        <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
                                            @foreach($keys as $key)
                                                @php $info = PermissionCatalog::label($key); @endphp
                                                <span class="badge" style="background:{{ $info['color'] }}18;color:{{ $info['color'] }};border:1px solid {{ $info['color'] }}28;">
                                                    <i class="fa-solid {{ $info['icon'] }}" style="font-size:.65rem;margin-right:.25rem;"></i>{{ $info['label'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="actions" style="justify-content:flex-end;">
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline">
                                <i class="fa-solid fa-pen"></i> Sửa
                            </a>
                            @if($role->name !== 'admin')
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa chức vụ này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <i class="fa-solid fa-shield-halved"></i>
                            Chưa có chức vụ nào.
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $roles->links('vendor.pagination.admin') }}</div>
@endsection
