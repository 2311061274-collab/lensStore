@extends('layouts.admin')

@section('title', 'Thêm Ống Kính Mới')

@section('actions')
    <a href="{{ route('admin.products.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid-2">
            <!-- Cột 1: Thông tin cơ bản -->
            <div>
                <h3 class="card-title mb-4">Thông Tin Cơ Bản</h3>
                
                <div class="form-group">
                    <label for="name">Tên Ống Kính: <span style="color:red;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Ví dụ: Sony FE 24-70mm f/2.8 GM II" required>
                    @error('name')
                        <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">Danh Mục Phân Loại: <span style="color:red;">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', request('category_id')) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label for="sku">Mã SKU / Model:</label>
                        <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Ví dụ: SEL2470GM2">
                        @error('sku')
                            <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Trạng Thái: <span style="color:red;">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="in_stock" {{ old('status') == 'in_stock' ? 'selected' : '' }}>Còn hàng</option>
                            <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>Tạm hết hàng</option>
                        </select>
                        @error('status')
                            <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="price">Giá Bán (VNĐ): <span style="color:red;">*</span></label>
                        <input type="number" id="price" name="price" class="form-control" value="{{ old('price') }}" placeholder="Ví dụ: 49990000" required>
                        @error('price')
                            <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Cột 2: Thông số kỹ thuật & Hình ảnh -->
            <div>
                <h3 class="card-title mb-4">Thông Số & Hình Ảnh</h3>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label for="focal_length">Tiêu Cự:</label>
                        <input type="text" id="focal_length" name="focal_length" class="form-control" value="{{ old('focal_length') }}" placeholder="Ví dụ: 24-70mm">
                        @error('focal_length')
                            <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="aperture">Khẩu Độ Tối Đa:</label>
                        <input type="text" id="aperture" name="aperture" class="form-control" value="{{ old('aperture') }}" placeholder="Ví dụ: f/2.8">
                        @error('aperture')
                            <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="mount">Ngàm Tương Thích:</label>
                    <input type="text" id="mount" name="mount" class="form-control" value="{{ old('mount') }}" placeholder="Ví dụ: Sony E, Canon RF">
                    @error('mount')
                        <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Hình Ảnh Sản Phẩm (Xem trước):</label>
                    <div class="img-preview-card" style="border: 2px dashed var(--line); border-radius: 12px; padding: 12px; background: var(--surface-soft); text-align: center; position: relative; cursor: pointer; transition: border-color .2s;" onclick="document.getElementById('image_file').click()">
                        <img id="imgPreview" src="{{ old('image_url') ?: 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=500&auto=format&fit=crop&q=80' }}" alt="Xem trước ảnh" style="max-height: 180px; width: 100%; object-fit: contain; border-radius: 8px; display: block; margin: 0 auto; background: #fff;">
                        <div style="margin-top: 8px; font-size: 0.8rem; font-weight: 600; color: var(--muted);" id="imgPreviewBadge">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Nhấn để chọn ảnh từ máy hoặc nhập URL bên dưới
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image_file">Tải Ảnh Từ Máy Tính:</label>
                    <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*" onchange="previewCreateImg(this)">
                    @error('image_file')
                        <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image_url">Hoặc Nhập Link Ảnh Online (URL):</label>
                    <input type="url" id="image_url" name="image_url" class="form-control" value="{{ old('image_url') }}" placeholder="https://images.unsplash.com/..." oninput="previewCreateUrl(this.value)" onchange="previewCreateUrl(this.value)">
                    @error('image_url')
                        <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group mt-4">
            <label for="description">Mô Tả Chi Tiết:</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Nhập thông tin mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>
            @error('description')
                <div class="error-text" style="color: red; font-size: 0.85em; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <hr style="border:0; border-top:1px solid var(--line); margin: 2rem 0;">
        
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Lưu Ống Kính</button>
            <a href="{{ route('admin.products.index') }}" class="btn-secondary">Hủy bỏ</a>
        </div>
    </form>
</div>

<script>
function previewCreateImg(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imgPreview');
            preview.src = e.target.result;
            const badge = document.getElementById('imgPreviewBadge');
            if (badge) {
                badge.innerHTML = `<span style="color:var(--ok);font-weight:600;"><i class="fa-solid fa-circle-check"></i> Đã chọn ảnh: ${file.name}</span>`;
            }
        };
        reader.readAsDataURL(file);
        document.getElementById('image_url').value = '';
    }
}

function previewCreateUrl(url) {
    url = (url || '').trim();
    const preview = document.getElementById('imgPreview');
    const badge = document.getElementById('imgPreviewBadge');
    if (url.startsWith('http://') || url.startsWith('https://')) {
        preview.src = url;
        preview.onerror = function() {
            preview.src = 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=500&auto=format&fit=crop&q=80';
            if (badge) {
                badge.innerHTML = `<span style="color:var(--danger);font-weight:600;"><i class="fa-solid fa-triangle-exclamation"></i> Không thể tải ảnh từ URL này. Vui lòng kiểm tra lại.</span>`;
            }
        };
        preview.onload = function() {
            if (badge) {
                badge.innerHTML = `<span style="color:var(--primary);font-weight:600;"><i class="fa-solid fa-link"></i> Đang hiển thị ảnh từ URL</span>`;
            }
        };
        document.getElementById('image_file').value = '';
    } else if (url === '') {
        preview.src = 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=500&auto=format&fit=crop&q=80';
        if (badge) {
            badge.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> Nhấn để chọn ảnh từ máy hoặc nhập URL bên dưới`;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('image_url');
    if (urlInput && urlInput.value) {
        previewCreateUrl(urlInput.value);
    }
});
</script>
@endsection
