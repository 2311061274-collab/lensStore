@extends('layouts.admin')

@section('title', 'Khách hàng')
@section('subtitle', 'Quản lý khách hàng')

@section('content')
<!-- Filter bar -->
<div class="card mb-4">
    <form class="filters" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 2; min-width: 250px;">
            <label>Tìm theo tên / email / ĐT</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập từ khóa..." class="form-control" style="width: 100%;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label>Tất cả trạng thái</label>
            <select name="status" class="form-control">
                <option value="">Tất cả</option>
                <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Khoá</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label>Xác thực email</label>
            <select name="email_verified" class="form-control">
                <option value="">Tất cả</option>
                <option value="verified" @selected(request('email_verified') === 'verified')>Đã xác thực</option>
                <option value="unverified" @selected(request('email_verified') === 'unverified')>Chưa xác thực</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label>Từ ngày (đăng ký)</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" style="width: 100%;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label>Đến ngày</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" style="width: 100%;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label>Sắp xếp</label>
            <select name="sort" class="form-control">
                <option value="newest" @selected(request('sort') === 'newest' || !request('sort'))>Mới nhất</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
                <option value="orders_desc" @selected(request('sort') === 'orders_desc')>Nhiều đơn nhất</option>
                <option value="ltv_desc" @selected(request('sort') === 'ltv_desc')>Chi tiêu nhiều nhất</option>
            </select>
        </div>
        <div>
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter"></i> Lọc</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card flush" style="padding:0;overflow:x-auto;">
    <table class="data" style="min-width: 1000px;">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                <th style="width: 60px;">STT</th>
                <th>Khách hàng</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Đơn</th>
                <th>Chi tiêu</th>
                <th>Trạng thái</th>
                <th>ĐK lúc</th>
                <th style="text-align: right;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
        @forelse($customers as $index => $c)
            @php
                $ltv = (int) ($c->lifetime_value ?? 0);
                $isVerified = !is_null($c->email_verified_at);
            @endphp
            <tr>
                <td><input type="checkbox" class="row-checkbox" value="{{ $c->id }}"></td>
                <td>{{ $customers->firstItem() + $index }}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="{{ $c->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($c->name).'&background=random' }}" alt="{{ $c->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <strong>{{ $c->name }}</strong><br>
                            @if($isVerified)
                                <span class="badge" style="background: #dcfce7; color: #16a34a; font-size: 0.7rem; padding: 2px 6px;">Đã xác thực</span>
                            @else
                                <span class="badge" style="background: #f1f5f9; color: #64748b; font-size: 0.7rem; padding: 2px 6px;">Chưa xác thực</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="muted">{{ $c->email }}</td>
                <td>{{ $c->phone ?: '—' }}</td>
                <td>{{ $c->orders_count }}</td>
                <td><strong>{{ number_format($ltv, 0, ',', '.') }}₫</strong></td>
                <td>
                    @if($c->is_active)
                        <span class="badge" style="background: #dcfce7; color: #16a34a;"><i class="fa-solid fa-circle" style="font-size: 8px; margin-right: 4px;"></i> Hoạt động</span>
                    @else
                        <span class="badge" style="background: #fee2e2; color: #ef4444;"><i class="fa-solid fa-lock" style="font-size: 8px; margin-right: 4px;"></i> Khoá</span>
                    @endif
                </td>
                <td style="white-space: nowrap;">{{ $c->created_at->format('d/m/Y') }}</td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 5px;">
                        <a class="btn btn-sm btn-outline" href="{{ route('admin.customers.show', $c) }}">Xem</a>
                        <a class="btn btn-sm btn-outline" href="{{ route('admin.customers.show', $c) }}">Sửa</a>
                        
                        <form action="{{ route('admin.customers.toggleLock', $c) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn {{ $c->is_active ? 'khoá' : 'mở khoá' }} tài khoản này?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $c->is_active ? 'btn-outline' : 'btn-primary' }}">{{ $c->is_active ? 'Khoá' : 'Mở khoá' }}</button>
                        </form>

                        <form action="{{ route('admin.customers.destroy', $c) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xoá khách hàng này không? Hành động này không thể hoàn tác!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white; border-color: #ef4444;">Xoá</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="10"><div class="empty-state"><i class="fa-solid fa-users"></i>Không tìm thấy khách hàng nào.</div></td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $customers->links('vendor.pagination.admin') }}
</div>

<style>
    .form-control {
        padding: 0.5rem;
        border: 1px solid var(--border);
        border-radius: 4px;
        background: white;
    }
    .badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.375rem;
    }
</style>
@endsection
