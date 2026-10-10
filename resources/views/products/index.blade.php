@extends('layouts.admin')

@section('title', 'Sản phẩm')
@section('subtitle', 'Quản lý ống kính · lọc brand · cập nhật giá hàng loạt')

@section('actions')
<a href="{{ route('admin.products.create') }}" class="btn">
    <i class="fa-solid fa-plus"></i> Thêm ống kính
</a>
@endsection

@section('content')
<div class="page-header" style="display:none"></div>

<form class="filters" action="{{ route('admin.products.index') }}" method="GET">
    <div>
        <label>Tìm kiếm</label>
        <input type="text" name="search" class="form-control" placeholder="Tên lens, ngàm..." value="{{ request('search') }}">
    </div>
    <div>
        <label>Danh mục</label>
        <select name="category_id" class="form-control">
            <option value="">Tất cả</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Thương hiệu</label>
        <select name="brand" class="form-control">
            <option value="">Tất cả</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Tồn kho</label>
        <select name="stock_status" class="form-control">
            <option value="">Tất cả</option>
            <option value="alert" {{ request('stock_status') == 'alert' ? 'selected' : '' }}>Cảnh báo kho (&lt; 3)</option>
            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Hết hàng (0)</option>
            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Sắp hết (&lt; 3, &gt; 0)</option>
            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Còn hàng (&ge; 3)</option>
        </select>
    </div>
    <div>
        <label>Sắp xếp</label>
        <select name="sort" class="form-control">
            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm</option>
            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
        </select>
    </div>
    <button type="submit" class="btn btn-outline"><i class="fa-solid fa-filter"></i> Lọc</button>
    @if(request()->hasAny(['search','category_id','brand','sort','stock_status']))
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline">Đặt lại</a>
    @endif
</form>

<div class="card mb-4">
    <div class="card-head"><div class="card-title">Cập nhật giá hàng loạt</div></div>
    <form action="{{ route('admin.products.bulk') }}" method="POST" class="filters" style="margin:0;padding:0;border:none;box-shadow:none;background:transparent;">
        @csrf
        <div>
            <label>Thương hiệu</label>
            <select name="brand" class="form-control" required>
                <option value="">Chọn thương hiệu</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand }}">{{ $brand }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>% giá (+/−)</label>
            <input type="number" step="any" name="price_percent" class="form-control" placeholder="VD: 10 hoặc -5">
        </div>
        <div>
            <label>Giá cố định</label>
            <input type="number" step="any" name="fixed_price" class="form-control" placeholder="Tùy chọn">
        </div>
        <button type="submit" class="btn" onclick="return confirm('Áp dụng cho tất cả sản phẩm thương hiệu này?');">
            <i class="fa-solid fa-bolt"></i> Áp dụng
        </button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th width="60">Ảnh</th>
                    <th>Tên Ống Kính</th>
                    <th>Danh Mục</th>
                    <th>Thông Số</th>
                    <th>Giá Bán</th>
                    <th>Trạng Thái</th>
                    <th width="120" style="text-align: right;">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td style="text-align: center;">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="45" height="45" style="object-fit: cover; border-radius: 6px; box-shadow: var(--shadow-sm);">
                    </td>
                    <td>
                        <strong class="text-main">
                            <a href="{{ route('admin.products.show', $product->id) }}" style="color: inherit; text-decoration: none;">{{ $product->name }}</a>
                        </strong>
                        @if($product->sku)
                            <div class="text-muted"><small>SKU: {{ $product->sku }}</small></div>
                        @endif
                    </td>
                    <td><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">{{ $product->category->name ?? 'Chưa phân loại' }}</span></td>
                    <td>
                        <div class="text-muted" style="font-size: 0.85rem;">
                            @if($product->focal_length)<span style="margin-right: 5px;" title="Tiêu cự"><i class="fa-solid fa-ruler-horizontal"></i> {{ $product->focal_length }}</span>@endif
                            @if($product->aperture)<span style="margin-right: 5px;" title="Khẩu độ"><i class="fa-solid fa-camera"></i> {{ $product->aperture }}</span>@endif
                            @if($product->mount)<span title="Ngàm"><i class="fa-solid fa-circle-notch"></i> {{ $product->mount }}</span>@endif
                        </div>
                    </td>
                    <td><strong style="color: var(--accent);">{{ $product->formatted_price }}</strong></td>
                    <td>
                        @if($product->stock > 0 && $product->status == 'in_stock')
                            <span style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                                <i class="fa-solid fa-check"></i> Còn hàng ({{ $product->stock }})
                            </span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 500;">
                                <i class="fa-solid fa-xmark"></i> Hết hàng
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-secondary btn-sm" title="Xem">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ống kính này?');">
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
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fa-solid fa-box-open"></i>
                            <h3>Chưa có sản phẩm nào</h3>
                            <p>Hãy thêm sản phẩm ống kính đầu tiên của bạn.</p>
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
    {{ $products->links('vendor.pagination.admin') }}
</div>
@endsection
