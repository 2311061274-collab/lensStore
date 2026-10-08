@extends('layouts.admin')

@section('title', $category->name)

@section('actions')
    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn">
        <i class="fa-solid fa-pen"></i> Chỉnh sửa
    </a>
    <a href="{{ route('admin.products.create') }}" class="btn btn-secondary">
        <i class="fa-solid fa-plus"></i> Thêm ống kính
    </a>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
@endsection

@push('styles')
<style>
    .cat-show-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.5rem;
        align-items: start;
    }
    .section-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .section-header {
        padding: .9rem 1.35rem;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        gap: .6rem;
        background: var(--surface-soft);
    }
    .section-header i { color: var(--primary); font-size: .85rem; }
    .section-header h3 { font-size: .85rem; font-weight: 700; color: var(--ink); flex: 1; }
    .section-header .badge-count {
        background: var(--primary);
        color: #fff;
        font-size: .68rem;
        font-weight: 700;
        padding: .15rem .55rem;
        border-radius: 999px;
    }
    .section-body { padding: 1.35rem; }

    /* Products table */
    .prod-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
    .prod-table th {
        font-size: .65rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--muted);
        font-weight: 700;
        padding: .7rem .9rem;
        background: var(--surface-soft);
        border-bottom: 1px solid var(--line);
        text-align: left;
        white-space: nowrap;
    }
    .prod-table td { padding: .75rem .9rem; border-bottom: 1px solid var(--line); vertical-align: middle; }
    .prod-table tbody tr:last-child td { border-bottom: none; }
    .prod-table tbody tr:hover td { background: #f8faff; }
    .prod-thumb { width: 42px; height: 42px; border-radius: 9px; object-fit: cover; box-shadow: var(--shadow-sm); }

    .spec-pill {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        background: var(--surface-soft);
        border: 1px solid var(--line);
        border-radius: 5px;
        padding: .15rem .5rem;
        font-size: .7rem;
        color: var(--muted);
        font-weight: 500;
        margin-right: .2rem;
    }

    .status-dot {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        font-size: .75rem;
        font-weight: 600;
        padding: .25rem .6rem;
        border-radius: 6px;
    }
    .status-dot.ok { background: var(--ok-soft); color: var(--ok); }
    .status-dot.bad { background: var(--danger-soft); color: var(--danger); }

    /* Sidebar cards */
    .info-row { display: flex; flex-direction: column; gap: .8rem; }
    .info-entry {}
    .info-entry .lbl { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); margin-bottom: .2rem; }
    .info-entry .val { font-size: .88rem; font-weight: 600; color: var(--ink); }

    .big-stat {
        text-align: center;
        padding: 1.25rem 1rem;
        background: linear-gradient(135deg, var(--primary), #6366f1);
        border-radius: 12px;
        color: #fff;
        margin-bottom: 1rem;
    }
    .big-stat .num { font-size: 2.8rem; font-weight: 900; line-height: 1; }
    .big-stat .lbl { font-size: .78rem; font-weight: 600; opacity: .85; margin-top: .3rem; }

    @media(max-width:900px) { .cat-show-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="cat-show-layout">

    {{-- LEFT: Products list --}}
    <div>
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-camera"></i>
                <h3>Danh sách ống kính</h3>
                <span class="badge-count">{{ $category->products->count() }} sản phẩm</span>
            </div>

            @if($category->products->isEmpty())
                <div class="section-body">
                    <div style="text-align:center;padding:3rem 1rem;color:var(--muted);">
                        <i class="fa-solid fa-box-open" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:.75rem;"></i>
                        <p style="font-weight:600;">Chưa có ống kính nào trong danh mục này.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn" style="margin-top:.75rem;display:inline-flex;">
                            <i class="fa-solid fa-plus"></i> Thêm ống kính đầu tiên
                        </a>
                    </div>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th width="52">Ảnh</th>
                                <th>Tên ống kính</th>
                                <th>Thông số</th>
                                <th>Ngàm</th>
                                <th>Giá bán</th>
                                <th>Tồn kho</th>
                                <th style="text-align:right;" width="100">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $product)
                            <tr>
                                <td>
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="prod-thumb">
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                       style="font-weight:700;color:var(--ink);text-decoration:none;display:block;">
                                        {{ $product->name }}
                                    </a>
                                    @if($product->sku)
                                        <span style="font-size:.7rem;color:var(--muted);">{{ $product->sku }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->focal_length)
                                        <span class="spec-pill"><i class="fa-solid fa-ruler-horizontal"></i> {{ $product->focal_length }}</span>
                                    @endif
                                    @if($product->aperture)
                                        <span class="spec-pill"><i class="fa-solid fa-camera"></i> {{ $product->aperture }}</span>
                                    @endif
                                </td>
                                <td style="color:var(--muted);font-size:.8rem;">{{ $product->mount ?? '—' }}</td>
                                <td><strong style="color:var(--danger);">{{ $product->formatted_price }}</strong></td>
                                <td>
                                    @if($product->stock > 0 && $product->status == 'in_stock')
                                        <span class="status-dot ok"><i class="fa-solid fa-check"></i> {{ $product->stock }}</span>
                                    @else
                                        <span class="status-dot bad"><i class="fa-solid fa-ban"></i> Hết</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex;gap:.35rem;justify-content:flex-end;">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                           class="btn btn-secondary btn-sm" title="Xem chi tiết">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                           class="btn btn-sm" title="Chỉnh sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Big stat --}}
        <div class="section-card">
            <div class="section-body" style="padding:1.25rem;">
                <div class="big-stat">
                    <div class="num">{{ $category->products->count() }}</div>
                    <div class="lbl">Ống kính trong danh mục</div>
                </div>
                <div class="info-row">
                    <div class="info-entry">
                        <div class="lbl">Mã danh mục</div>
                        <div class="val">#{{ $category->id }}</div>
                    </div>
                    <div class="info-entry">
                        <div class="lbl">Tên danh mục</div>
                        <div class="val">{{ $category->name }}</div>
                    </div>
                    @if($category->description)
                    <div class="info-entry">
                        <div class="lbl">Mô tả</div>
                        <div class="val" style="font-weight:400;font-size:.83rem;color:var(--muted);line-height:1.6;">{{ $category->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Time --}}
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-clock"></i>
                <h3>Thời gian</h3>
            </div>
            <div class="section-body">
                <div class="info-row">
                    <div class="info-entry">
                        <div class="lbl">Ngày tạo</div>
                        <div class="val">{{ $category->created_at->format('d/m/Y') }}</div>
                        <div style="font-size:.72rem;color:var(--muted);">{{ $category->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="info-entry">
                        <div class="lbl">Cập nhật lần cuối</div>
                        <div class="val">{{ $category->updated_at->format('d/m/Y') }}</div>
                        <div style="font-size:.72rem;color:var(--muted);">{{ $category->updated_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="section-card" style="border-color:#fecaca;">
            <div class="section-header" style="background:#fef2f2;border-color:#fecaca;">
                <i class="fa-solid fa-triangle-exclamation" style="color:var(--danger);"></i>
                <h3 style="color:var(--danger);">Vùng nguy hiểm</h3>
            </div>
            <div class="section-body">
                <p style="font-size:.8rem;color:var(--muted);margin-bottom:.85rem;line-height:1.6;">
                    Xóa danh mục này sẽ không thể hoàn tác. Danh mục chỉ có thể xóa khi không còn sản phẩm nào.
                </p>
                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;"
                            @if($category->products->count() > 0) disabled title="Không thể xóa khi còn sản phẩm" @endif>
                        <i class="fa-solid fa-trash"></i> Xóa danh mục
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
