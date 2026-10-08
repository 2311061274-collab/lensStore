@extends('layouts.admin')

@section('title', $product->name)

@section('actions')
    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn">
        <i class="fa-solid fa-pen"></i> Chỉnh sửa
    </a>
    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;"
          onsubmit="return confirm('Bạn có chắc chắn muốn xóa ống kính này?');">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="fa-solid fa-trash"></i> Xóa
        </button>
    </form>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
@endsection

@push('styles')
<style>
    .show-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
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
    .section-header h3 { font-size: .85rem; font-weight: 700; color: var(--ink); }
    .section-body { padding: 1.35rem; }

    /* Hero image */
    .product-hero {
        position: relative;
        width: 100%;
        height: 280px;
        overflow: hidden;
        border-radius: var(--radius);
        background: var(--surface-soft);
    }
    .product-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15,23,42,.7) 0%, transparent 55%);
    }
    .product-hero-info {
        position: absolute;
        bottom: 1.25rem;
        left: 1.25rem;
        right: 1.25rem;
        color: #fff;
    }
    .product-hero-info h2 { font-size: 1.3rem; font-weight: 800; line-height: 1.3; margin-bottom: .35rem; }
    .product-hero-chips { display: flex; gap: .4rem; flex-wrap: wrap; }
    .hero-chip {
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        backdrop-filter: blur(6px);
        color: #fff;
        font-size: .7rem;
        font-weight: 600;
        padding: .2rem .55rem;
        border-radius: 6px;
    }

    /* Info grid */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .info-item {}
    .info-item .lbl { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); margin-bottom: .2rem; }
    .info-item .val { font-size: .92rem; font-weight: 600; color: var(--ink); }
    .info-item .val.price { font-size: 1.2rem; font-weight: 800; color: var(--danger); }
    .info-item .val.stock-ok { color: var(--ok); }
    .info-item .val.stock-bad { color: var(--danger); }

    /* Description */
    .desc-box {
        background: var(--surface-soft);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 1rem;
        font-size: .88rem;
        color: var(--ink-soft);
        line-height: 1.7;
    }

    /* Related table */
    .related-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
    .related-table th {
        font-size: .65rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--muted);
        font-weight: 700;
        padding: .65rem .9rem;
        background: var(--surface-soft);
        border-bottom: 1px solid var(--line);
        text-align: left;
    }
    .related-table td { padding: .65rem .9rem; border-bottom: 1px solid var(--line); vertical-align: middle; }
    .related-table tbody tr:last-child td { border-bottom: none; }
    .related-table tbody tr:hover td { background: var(--surface-soft); }
    .related-thumb { width: 38px; height: 38px; border-radius: 8px; object-fit: cover; }

    /* Side stats */
    .stat-mini { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
    .stat-mini-item {
        background: var(--surface-soft);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: .85rem .9rem;
        text-align: center;
    }
    .stat-mini-item .num { font-size: 1.3rem; font-weight: 800; line-height: 1; }
    .stat-mini-item .lbl { font-size: .68rem; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-top: .2rem; }

    @media(max-width:900px) { .show-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="show-layout">

    {{-- LEFT: Main info --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Hero image + name --}}
        <div class="product-hero">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            <div class="product-hero-overlay"></div>
            <div class="product-hero-info">
                <h2>{{ $product->name }}</h2>
                <div class="product-hero-chips">
                    @if($product->brand)<span class="hero-chip"><i class="fa-solid fa-building"></i> {{ $product->brand }}</span>@endif
                    @if($product->focal_length)<span class="hero-chip"><i class="fa-solid fa-ruler-horizontal"></i> {{ $product->focal_length }}</span>@endif
                    @if($product->aperture)<span class="hero-chip"><i class="fa-solid fa-camera"></i> {{ $product->aperture }}</span>@endif
                    @if($product->mount)<span class="hero-chip"><i class="fa-solid fa-circle-notch"></i> {{ $product->mount }}</span>@endif
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-circle-info"></i>
                <h3>Thông tin chi tiết</h3>
            </div>
            <div class="section-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="lbl">ID Sản phẩm</div>
                        <div class="val">#{{ $product->id }}</div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Mã SKU</div>
                        <div class="val">{{ $product->sku ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Danh mục</div>
                        <div class="val">
                            <a href="{{ route('admin.categories.show', $product->category_id) }}"
                               style="color:var(--primary);text-decoration:none;font-weight:600;">
                                {{ $product->category->name ?? '—' }}
                            </a>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Trạng thái</div>
                        <div class="val @if($product->stock > 0 && $product->status == 'in_stock') stock-ok @else stock-bad @endif">
                            @if($product->stock > 0 && $product->status == 'in_stock')
                                <i class="fa-solid fa-circle-check"></i> Còn hàng
                            @else
                                <i class="fa-solid fa-ban"></i> Hết hàng
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Tiêu cự</div>
                        <div class="val">{{ $product->focal_length ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Khẩu độ</div>
                        <div class="val">{{ $product->aperture ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Ngàm gắn</div>
                        <div class="val">{{ $product->mount ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="lbl">Ngày tạo</div>
                        <div class="val">{{ $product->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if($product->description)
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-align-left"></i>
                <h3>Mô tả sản phẩm</h3>
            </div>
            <div class="section-body">
                <div class="desc-box">{{ $product->description }}</div>
            </div>
        </div>
        @endif

        {{-- Related products --}}
        @if($relatedProducts->count() > 0)
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-layer-group"></i>
                <h3>Ống kính cùng danh mục — {{ $product->category->name }}</h3>
            </div>
            <div style="overflow-x:auto;">
                <table class="related-table">
                    <thead>
                        <tr>
                            <th width="52">Ảnh</th>
                            <th>Tên ống kính</th>
                            <th>Tiêu cự</th>
                            <th>Khẩu độ</th>
                            <th>Giá bán</th>
                            <th width="80" style="text-align:right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatedProducts as $item)
                        <tr>
                            <td>
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="related-thumb">
                            </td>
                            <td>
                                <a href="{{ route('admin.products.show', $item->id) }}"
                                   style="font-weight:600;color:var(--ink);text-decoration:none;">
                                    {{ $item->name }}
                                </a>
                            </td>
                            <td style="color:var(--muted);">{{ $item->focal_length ?? '—' }}</td>
                            <td style="color:var(--muted);">{{ $item->aperture ?? '—' }}</td>
                            <td><strong style="color:var(--danger);">{{ $item->formatted_price }}</strong></td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.products.show', $item->id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT: Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Price & Stock --}}
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-tags"></i>
                <h3>Giá & Tồn kho</h3>
            </div>
            <div class="section-body">
                <div style="text-align:center;padding:.75rem 0;">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.4rem;">Giá bán</div>
                    <div style="font-size:2rem;font-weight:900;color:var(--danger);letter-spacing:-.03em;">{{ $product->formatted_price }}</div>
                </div>
                <div class="stat-mini" style="margin-top:.75rem;">
                    <div class="stat-mini-item">
                        <div class="num" style="color:var(--primary);">{{ $product->stock }}</div>
                        <div class="lbl">Tồn kho</div>
                    </div>
                    <div class="stat-mini-item">
                        <div class="num" style="color:var(--ok);">
                            @if($product->stock > 0 && $product->status == 'in_stock')
                                <i class="fa-solid fa-check" style="font-size:1.1rem;"></i>
                            @else
                                <i class="fa-solid fa-xmark" style="font-size:1.1rem;color:var(--danger);"></i>
                            @endif
                        </div>
                        <div class="lbl">Trạng thái</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-bolt"></i>
                <h3>Thao tác nhanh</h3>
            </div>
            <div class="section-body" style="display:flex;flex-direction:column;gap:.6rem;">
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-pen"></i> Chỉnh sửa ống kính
                </a>
                <a href="{{ route('storefront.show', $product->id) }}" target="_blank" class="btn btn-secondary" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem ngoài cửa hàng
                </a>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa ống kính này?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">
                        <i class="fa-solid fa-trash"></i> Xóa sản phẩm
                    </button>
                </form>
            </div>
        </div>

        {{-- Meta --}}
        <div class="section-card">
            <div class="section-header">
                <i class="fa-solid fa-clock"></i>
                <h3>Thông tin thời gian</h3>
            </div>
            <div class="section-body" style="display:flex;flex-direction:column;gap:.75rem;">
                <div>
                    <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.2rem;">Ngày tạo</div>
                    <div style="font-size:.88rem;font-weight:600;">{{ $product->created_at->format('d/m/Y — H:i') }}</div>
                    <div style="font-size:.75rem;color:var(--muted);">{{ $product->created_at->diffForHumans() }}</div>
                </div>
                <div>
                    <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.2rem;">Cập nhật lần cuối</div>
                    <div style="font-size:.88rem;font-weight:600;">{{ $product->updated_at->format('d/m/Y — H:i') }}</div>
                    <div style="font-size:.75rem;color:var(--muted);">{{ $product->updated_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
