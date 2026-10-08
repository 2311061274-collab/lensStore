@extends('layouts.admin')

@section('title', 'Quản lý Danh mục Ống kính')

@section('content')
<div class="page-header">
    <h1>Quản Lý Danh Mục Ống Kính</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm Danh Mục Mới
    </a>
</div>

<div class="card mb-4">
    <!-- Form tìm kiếm đơn giản -->
    <form action="{{ route('admin.categories.index') }}" method="GET" class="flex items-center gap-4">
        <div class="flex items-center gap-2">
            <input type="text" name="search" class="form-control" placeholder="Nhập tên hoặc mô tả danh mục..." value="{{ request('search') }}" style="max-width: 300px;">
        </div>
        
        <button type="submit" class="btn btn-secondary">
            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
        </button>
        
        @if(request('search'))
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary" style="background:transparent; border:none; text-decoration:underline;">Xóa tìm kiếm</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th width="260">Tên Danh Mục</th>
                    <th>Mô Tả</th>
                    <th width="180" style="text-align: center;">Số Lượng Ống Kính</th>
                    <th width="150" style="text-align: right;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>
                        <strong class="text-main">
                            <a href="{{ route('admin.categories.show', $category->id) }}" style="color: inherit; text-decoration: none;">{{ $category->name }}</a>
                        </strong>
                    </td>
                    <td class="text-muted">{{ $category->description ?? 'Chưa có mô tả' }}</td>
                    <td style="text-align: center;">
                        <span style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            {{ $category->products_count }} sản phẩm
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-secondary btn-sm" title="Xem">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm" title="Sửa" style="background: #3b82f6;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
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
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fa-solid fa-tags"></i>
                            <h3>Chưa có danh mục nào</h3>
                            <p>Hãy thêm danh mục đầu tiên cho hệ thống của bạn.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Phân trang -->
<div class="mt-4">
    {{ $categories->links('vendor.pagination.admin') }}
</div>
@endsection
