@extends('layouts.admin')

@section('title', 'Sửa: ' . $product->name)

@section('actions')
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
@endsection

@push('styles')
<style>
    .edit-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 1.5rem;
        align-items: start;
    }
    .form-panel { display: flex; flex-direction: column; gap: 1.25rem; }
    .section-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .section-header {
        padding: 1rem 1.35rem;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        gap: .6rem;
        background: var(--surface-soft);
    }
    .section-header i { color: var(--primary); font-size: .9rem; }
    .section-header h3 { font-size: .88rem; font-weight: 700; color: var(--ink); }
    .section-body { padding: 1.35rem; display: flex; flex-direction: column; gap: 1rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-row.triple { grid-template-columns: 1fr 1fr 1fr; }

    .field label {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--muted);
        margin-bottom: .4rem;
    }
    .field label .req { color: var(--danger); font-size: .9rem; line-height: 1; }
    .field input, .field select, .field textarea {
        width: 100%;
        padding: .6rem .85rem;
        border: 1.5px solid var(--line);
        border-radius: 10px;
        font-size: .88rem;
        color: var(--ink);
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
        font-family: inherit;
    }
    .field input:focus, .field select:focus, .field textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    }
    .field textarea { resize: vertical; min-height: 90px; }
    .field .error-text { font-size: .75rem; color: var(--danger); margin-top: .3rem; font-weight: 500; }

    /* Sidebar right */
    .sidebar-panel { display: flex; flex-direction: column; gap: 1.25rem; }

    /* Image upload */
    .img-preview-wrap {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: var(--surface-soft);
        border: 2px dashed var(--line);
        cursor: pointer;
        transition: border-color .2s;
    }
    .img-preview-wrap:hover { border-color: var(--primary); }
    .img-preview-wrap img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }
    .img-overlay {
        position: absolute;
        inset: 0;
        background: rgba(79,70,229,.65);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: .82rem;
        font-weight: 600;
        gap: .5rem;
        opacity: 0;
        transition: opacity .2s;
    }
    .img-overlay i { font-size: 1.5rem; }
    .img-preview-wrap:hover .img-overlay { opacity: 1; }
    .img-or { text-align: center; font-size: .75rem; font-weight: 600; color: var(--muted); margin: .6rem 0; display: flex; align-items: center; gap: .5rem; }
    .img-or::before, .img-or::after { content: ''; flex: 1; height: 1px; background: var(--line); }

    /* Status toggle */
    .status-toggle { display: flex; gap: .5rem; }
    .status-opt input { display: none; }
    .status-opt label {
        display: flex;
        align-items: center;
        gap: .4rem;
        padding: .5rem .9rem;
        border: 1.5px solid var(--line);
        border-radius: 9px;
        cursor: pointer;
        font-size: .82rem;
        font-weight: 600;
        color: var(--muted);
        transition: all .15s;
        background: var(--surface-soft);
    }
    .status-opt input:checked + label { border-color: var(--ok); color: var(--ok); background: var(--ok-soft); }
    .status-opt.danger input:checked + label { border-color: var(--danger); color: var(--danger); background: var(--danger-soft); }

    /* Price highlight */
    .price-input-wrap { position: relative; }
    .price-input-wrap input { padding-right: 3rem; }
    .price-unit {
        position: absolute;
        right: .85rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: .75rem;
        font-weight: 700;
        color: var(--muted);
        pointer-events: none;
    }

    /* Actions footer */
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .75rem;
        padding: 1rem 1.35rem;
        background: var(--surface-soft);
        border-top: 1px solid var(--line);
        border-radius: 0 0 var(--radius) var(--radius);
    }

    @media(max-width:1000px) {
        .edit-layout { grid-template-columns: 1fr; }
        .form-row.triple { grid-template-columns: 1fr 1fr; }
    }
    @media(max-width:640px) {
        .form-row, .form-row.triple { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

@if($errors->any())
<div class="alert alert-error" style="margin-bottom:1rem;">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <strong>Có lỗi xảy ra:</strong> Vui lòng kiểm tra lại các trường được đánh dấu.
</div>
@endif

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1rem;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    @method('PUT')

    <div class="edit-layout">
        {{-- LEFT: Main form --}}
        <div class="form-panel">

            {{-- Thông tin cơ bản --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-circle-info"></i>
                    <h3>Thông tin cơ bản</h3>
                </div>
                <div class="section-body">
                    <div class="field">
                        <label>Tên ống kính <span class="req">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required placeholder="VD: Sony FE 50mm f/1.2 GM">
                        @error('name')<div class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
                    </div>
                    <div class="form-row">
                        <div class="field">
                            <label>Danh mục <span class="req">*</span></label>
                            <select name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Mã SKU / Model</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="VD: SKU-A1B2C3">
                            @error('sku')<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thông số kỹ thuật --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-sliders"></i>
                    <h3>Thông số kỹ thuật</h3>
                </div>
                <div class="section-body">
                    <div class="form-row triple">
                        <div class="field">
                            <label>Tiêu cự</label>
                            <input type="text" name="focal_length" value="{{ old('focal_length', $product->focal_length) }}" placeholder="VD: 50mm">
                            @error('focal_length')<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Khẩu độ tối đa</label>
                            <input type="text" name="aperture" value="{{ old('aperture', $product->aperture) }}" placeholder="VD: f/1.2">
                            @error('aperture')<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Ngàm tương thích</label>
                            <input type="text" name="mount" value="{{ old('mount', $product->mount) }}" placeholder="VD: Sony E">
                            @error('mount')<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mô tả sản phẩm --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-align-left"></i>
                    <h3>Mô tả sản phẩm</h3>
                </div>
                <div class="section-body">
                    <div class="field">
                        <label>Mô tả chi tiết</label>
                        <textarea name="description" rows="6" placeholder="Nhập mô tả đặc điểm, tính năng nổi bật, ứng dụng của ống kính...">{{ old('description', $product->description) }}</textarea>
                        @error('description')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    {{-- Các góc ảnh của ống kính --}}
                    <div class="field" style="margin-top: 1.5rem;">
                        <label>Các góc ảnh của ống kính</label>
                        <div id="gallery-wrapper">
                            @php $gallery = is_array($product->gallery_images) ? $product->gallery_images : []; @endphp
                            @foreach($gallery as $i => $g)
                            <div class="gallery-row" style="display:flex; gap:10px; margin-bottom:10px; align-items:center;">
                                <img src="{{ str_starts_with($g['url'] ?? '', 'http') ? ($g['url'] ?? '') : asset($g['url'] ?? '') }}" style="width:50px; height:50px; object-fit:cover; border-radius:4px; {{ empty($g['url']) ? 'display:none;' : '' }}" onerror="this.style.display='none'">
                                <input type="text" name="gallery_urls[]" value="{{ $g['url'] ?? '' }}" placeholder="Hoặc dán URL ảnh..." style="flex:1.5;" oninput="updateRowThumb(this)">
                                <input type="file" name="gallery_files[]" accept="image/*" style="flex:1;" onchange="updateFileThumb(this)">
                                <input type="text" name="gallery_caps[]" value="{{ $g['cap'] ?? '' }}" placeholder="Caption (Nhãn)" style="flex:1.5;">
                                <button type="button" onclick="this.parentElement.remove()" class="btn btn-secondary" style="padding: 5px 10px;"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addGalleryRow()" class="btn btn-secondary btn-sm" style="margin-top: 10px; padding: 6px 12px;"><i class="fa-solid fa-plus"></i> Thêm ảnh</button>
                    </div>

                    {{-- Ảnh mẫu thực tế --}}
                    <div class="field" style="margin-top: 2rem;">
                        <label>Ảnh mẫu thực tế</label>
                        <div id="sample-wrapper">
                            @php $samples = is_array($product->sample_images) ? $product->sample_images : []; @endphp
                            @foreach($samples as $i => $s)
                            <div class="sample-row" style="display:flex; gap:10px; margin-bottom:10px; align-items:center;">
                                <img src="{{ str_starts_with($s['url'] ?? '', 'http') ? ($s['url'] ?? '') : asset($s['url'] ?? '') }}" style="width:50px; height:50px; object-fit:cover; border-radius:4px; {{ empty($s['url']) ? 'display:none;' : '' }}" onerror="this.style.display='none'">
                                <input type="text" name="sample_urls[]" value="{{ $s['url'] ?? '' }}" placeholder="Hoặc dán URL ảnh..." style="flex:1.5;" oninput="updateRowThumb(this)">
                                <input type="file" name="sample_files[]" accept="image/*" style="flex:1;" onchange="updateFileThumb(this)">
                                <input type="text" name="sample_tags[]" value="{{ $s['tag'] ?? '' }}" placeholder="Thẻ (VD: Chân dung)" style="flex:1;">
                                <input type="text" name="sample_texts[]" value="{{ $s['text'] ?? '' }}" placeholder="Mô tả ảnh" style="flex:1.5;">
                                <button type="button" onclick="this.parentElement.remove()" class="btn btn-secondary" style="padding: 5px 10px;"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addSampleRow()" class="btn btn-secondary btn-sm" style="margin-top: 10px; padding: 6px 12px;"><i class="fa-solid fa-plus"></i> Thêm ảnh mẫu</button>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark"></i> Hủy bỏ
                    </a>
                    <button type="submit" class="btn">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                    </button>
                </div>
            </div>

        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="sidebar-panel">

            {{-- Giá & Tồn kho --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-tags"></i>
                    <h3>Giá & Tồn kho</h3>
                </div>
                <div class="section-body">
                    <div class="field">
                        <label>Giá bán (VNĐ) <span class="req">*</span></label>
                        <div class="price-input-wrap">
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" step="1000" placeholder="0">
                            <span class="price-unit">₫</span>
                        </div>
                        @error('price')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Số lượng tồn kho <span class="req">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0" placeholder="0">
                        @error('stock')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-circle-dot"></i>
                    <h3>Trạng thái</h3>
                </div>
                <div class="section-body">
                    <div class="status-toggle">
                        <div class="status-opt">
                            <input type="radio" name="status" id="s_instock" value="in_stock" {{ old('status', $product->status) == 'in_stock' ? 'checked' : '' }}>
                            <label for="s_instock"><i class="fa-solid fa-check-circle"></i> Còn hàng</label>
                        </div>
                        <div class="status-opt danger">
                            <input type="radio" name="status" id="s_oos" value="out_of_stock" {{ old('status', $product->status) == 'out_of_stock' ? 'checked' : '' }}>
                            <label for="s_oos"><i class="fa-solid fa-ban"></i> Hết hàng</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hình ảnh --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-image"></i>
                    <h3>Hình ảnh sản phẩm</h3>
                </div>
                <div class="section-body">
                    <div class="img-preview-wrap" onclick="document.getElementById('image_file').click()">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" id="imgPreview">
                        <div class="img-overlay">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Nhấn để chọn ảnh mới từ máy tính</span>
                        </div>
                    </div>
                    <div id="imgSourceBadge" style="font-size:0.78rem; text-align:center; margin-top:6px; font-weight:600; color:var(--muted);">
                        @if(str_starts_with($product->image ?? '', 'http'))
                            <span style="color:var(--primary);"><i class="fa-solid fa-link"></i> Đang dùng link URL</span>
                        @elseif(!empty($product->image))
                            <span style="color:var(--ok);"><i class="fa-solid fa-image"></i> Đang dùng ảnh tải lên</span>
                        @else
                            <span><i class="fa-solid fa-camera"></i> Ảnh mặc định</span>
                        @endif
                    </div>
                    <input type="file" id="image_file" name="image_file" accept="image/*" style="display:none" onchange="previewImg(this)">
                    <div class="img-or">hoặc dán URL</div>
                    <div class="field">
                        <input type="url" name="image_url" id="image_url_input" value="{{ old('image_url', str_starts_with($product->image ?? '', 'http') ? $product->image : '') }}" placeholder="https://..." oninput="previewFromUrl(this.value)" onchange="previewFromUrl(this.value)">
                        @error('image_url')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    @error('image_file')<div class="error-text">{{ $message }}</div>@enderror
                </div>
            </div>

        </div>
    </div>
</form>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = e => { 
            document.getElementById('imgPreview').src = e.target.result; 
            const badge = document.getElementById('imgSourceBadge');
            if (badge) badge.innerHTML = `<span style="color:var(--ok);"><i class="fa-solid fa-circle-check"></i> Đã chọn file từ máy: ${file.name}</span>`;
        };
        reader.readAsDataURL(file);
        document.getElementById('image_url_input').value = '';
    }
}

function previewFromUrl(url) {
    url = (url || '').trim();
    const preview = document.getElementById('imgPreview');
    const badge = document.getElementById('imgSourceBadge');
    if (url.startsWith('http://') || url.startsWith('https://')) {
        preview.src = url;
        preview.onerror = function() {
            preview.src = "{{ $product->image_url }}";
            if (badge) badge.innerHTML = `<span style="color:var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Không tải được ảnh từ URL này</span>`;
        };
        preview.onload = function() {
            if (badge) badge.innerHTML = `<span style="color:var(--primary);"><i class="fa-solid fa-link"></i> Đang hiển thị từ URL</span>`;
        };
        document.getElementById('image_file').value = '';
    } else if (url === '') {
        preview.src = "{{ $product->image_url }}";
        if (badge) badge.innerHTML = '@if(str_starts_with($product->image ?? '', 'http'))<span style="color:var(--primary);"><i class="fa-solid fa-link"></i> Đang dùng link URL</span>@elseif(!empty($product->image))<span style="color:var(--ok);"><i class="fa-solid fa-image"></i> Đang dùng ảnh tải lên</span>@else<span><i class="fa-solid fa-camera"></i> Ảnh mặc định</span>@endif';
    }
}

function updateRowThumb(input) {
    const row = input.closest('.gallery-row, .sample-row');
    const img = row ? row.querySelector('img') : null;
    if (!img) return;
    const url = input.value.trim();
    if (url.startsWith('http://') || url.startsWith('https://')) {
        img.src = url;
        img.style.display = 'block';
    } else if (!url) {
        img.style.display = 'none';
    }
}

function updateFileThumb(input) {
    const row = input.closest('.gallery-row, .sample-row');
    const img = row ? row.querySelector('img') : null;
    if (!img || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

function addGalleryRow() {
    const wrap = document.getElementById('gallery-wrapper');
    const div = document.createElement('div');
    div.className = 'gallery-row';
    div.style.cssText = 'display:flex; gap:10px; margin-bottom:10px; align-items:center;';
    div.innerHTML = `
        <img src="" style="width:50px; height:50px; object-fit:cover; border-radius:4px; display:none;" onerror="this.style.display='none'">
        <input type="text" name="gallery_urls[]" value="" placeholder="Hoặc dán URL ảnh..." style="flex:1.5;" oninput="updateRowThumb(this)">
        <input type="file" name="gallery_files[]" accept="image/*" style="flex:1;" onchange="updateFileThumb(this)">
        <input type="text" name="gallery_caps[]" placeholder="Caption (Nhãn)" style="flex:1.5;">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-secondary" style="padding: 5px 10px;"><i class="fa-solid fa-trash"></i></button>
    `;
    wrap.appendChild(div);
}

function addSampleRow() {
    const wrap = document.getElementById('sample-wrapper');
    const div = document.createElement('div');
    div.className = 'sample-row';
    div.style.cssText = 'display:flex; gap:10px; margin-bottom:10px; align-items:center;';
    div.innerHTML = `
        <img src="" style="width:50px; height:50px; object-fit:cover; border-radius:4px; display:none;" onerror="this.style.display='none'">
        <input type="text" name="sample_urls[]" value="" placeholder="Hoặc dán URL ảnh..." style="flex:1.5;" oninput="updateRowThumb(this)">
        <input type="file" name="sample_files[]" accept="image/*" style="flex:1;" onchange="updateFileThumb(this)">
        <input type="text" name="sample_tags[]" placeholder="Thẻ (VD: Chân dung)" style="flex:1;">
        <input type="text" name="sample_texts[]" placeholder="Mô tả ảnh" style="flex:1.5;">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-secondary" style="padding: 5px 10px;"><i class="fa-solid fa-trash"></i></button>
    `;
    wrap.appendChild(div);
}
</script>
@endsection
