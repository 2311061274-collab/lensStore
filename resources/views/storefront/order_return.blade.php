@extends('layouts.app')

@section('content')
<style>
    .return-container { max-width: 600px; margin: 3rem auto; padding: 0 1rem; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 2rem; border: 1px solid #e2e8f0; }
    .card-title { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 1.5rem; border-bottom: 2px solid #f8fafc; padding-bottom: 1rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 700; color: #475569; margin-bottom: 0.5rem; font-size: 0.95rem; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; }
    .form-control:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
    .btn-submit { background: #4f46e5; color: white; border: none; padding: 0.9rem 2rem; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; transition: opacity 0.2s; }
    .btn-submit:hover { opacity: 0.9; }
</style>

<div class="return-container">
    <div class="card">
        <h2 class="card-title">Yêu cầu Trả hàng / Hoàn tiền</h2>
        <p style="color:#64748b; margin-bottom: 1.5rem;">Đơn hàng <strong>#{{ $order->order_code ?? str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></p>

        @if(session('error'))
            <div style="background:#fee2e2;color:#991b1b;padding:1rem;border-radius:8px;margin-bottom:1rem;">{{ session('error') }}</div>
        @endif

        <form action="{{ route('orders.return.store', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Lý do trả hàng <span style="color:red">*</span></label>
                <select name="reason" class="form-control" required>
                    <option value="">-- Chọn lý do --</option>
                    <option value="Hàng lỗi, không hoạt động">Hàng lỗi, không hoạt động</option>
                    <option value="Giao sai mẫu mã, màu sắc">Giao sai mẫu mã, màu sắc</option>
                    <option value="Thiếu phụ kiện, linh kiện">Thiếu phụ kiện, linh kiện</option>
                    <option value="Hàng giả, hàng nhái">Hàng giả, hàng nhái</option>
                    <option value="Lý do khác">Lý do khác</option>
                </select>
                @error('reason')<div style="color:red; font-size:0.85rem; margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Hình ảnh bằng chứng (Tùy chọn)</label>
                
                {{-- Native hidden input --}}
                <input type="file" id="return_image_input" name="image" accept="image/jpeg,image/png,image/webp,image/jpg" style="display:none;">

                {{-- Empty state: Drag and drop / Click upload box --}}
                <div id="upload_dropzone" onclick="document.getElementById('return_image_input').click();" style="border:2px dashed #cbd5e1; border-radius:12px; background:#f8fafc; padding:1.5rem; text-align:center; cursor:pointer; transition:all 0.2s;">
                    <div style="width:48px; height:48px; margin:0 auto 0.75rem; border-radius:50%; background:rgba(79,70,229,0.1); color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:1.35rem;">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div style="font-weight:700; color:#334155; margin-bottom:0.25rem;">
                        Nhấn để chọn ảnh hoặc kéo thả vào đây
                    </div>
                    <div style="font-size:0.8rem; color:#64748b;">
                        Định dạng hỗ trợ: JPEG, PNG, WEBP · Tối đa 5MB
                    </div>
                    <button type="button" class="btn-choose" style="margin-top:0.75rem; background:#ffffff; border:1px solid #cbd5e1; color:#334155; padding:0.4rem 1rem; border-radius:6px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                        <i class="fa-regular fa-folder-open"></i> Choose file
                    </button>
                </div>

                {{-- Preview state: Show selected image thumbnail, name, size and action buttons --}}
                <div id="upload_preview_card" style="display:none; border:1px solid #e2e8f0; border-radius:12px; background:#ffffff; padding:1rem; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="position:relative; width:72px; height:72px; border-radius:8px; overflow:hidden; border:1px solid #cbd5e1; background:#0f172a; flex-shrink:0;">
                            <img id="preview_img" src="" alt="Bằng chứng" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div id="preview_filename" style="font-weight:700; color:#1e293b; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></div>
                            <div id="preview_filesize" style="font-size:0.78rem; color:#64748b; margin-top:2px;"></div>
                            <div style="display:flex; gap:8px; margin-top:8px;">
                                <button type="button" onclick="document.getElementById('return_image_input').click();" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#334155; padding:0.3rem 0.65rem; border-radius:6px; font-size:0.78rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="fa-solid fa-arrows-rotate"></i> Thay đổi
                                </button>
                                <button type="button" id="btn_remove_image" style="background:#fee2e2; border:1px solid #fecaca; color:#b91c1c; padding:0.3rem 0.65rem; border-radius:6px; font-size:0.78rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="fa-solid fa-trash-can"></i> Xóa ảnh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="client_upload_error" style="display:none; color:#dc2626; font-size:0.85rem; margin-top:6px; font-weight:600;"></div>
                @error('image')<div style="color:red; font-size:0.85rem; margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Ghi chú chi tiết</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Mô tả chi tiết tình trạng hàng hóa...">{{ old('note') }}</textarea>
            </div>

            <!-- THÔNG TIN TÀI KHOẢN NGÂN HÀNG NHẬN TIỀN HOÀN -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:1.25rem; margin-bottom:1.5rem;">
                <div style="font-weight:700; color:#1e293b; margin-bottom:0.75rem; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-building-columns" style="color:#4f46e5;"></i> Thông tin tài khoản nhận tiền hoàn
                </div>
                <div style="font-size:0.85rem; color:#64748b; margin-bottom:1rem; line-height:1.5;">
                    Số tiền dự kiến hoàn trả: <strong style="color:#ef4444; font-size:1rem;">{{ number_format($order->total, 0, ',', '.') }} ₫</strong><br>
                    LensStore sẽ thực hiện chuyển khoản sau khi yêu cầu đổi trả được chấp thuận và kiểm định chất lượng hàng hoàn tất.
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label" style="font-size:0.88rem;">Ngân hàng thụ hưởng <span style="color:red">*</span></label>
                    <input type="text" name="bank_name" class="form-control" list="bankList" placeholder="VD: Vietcombank, MB Bank, Techcombank, ACB..." value="{{ old('bank_name') }}" required>
                    <datalist id="bankList">
                        <option value="Vietcombank (VCB)">
                        <option value="MB Bank (Quân Đội)">
                        <option value="Techcombank (TCB)">
                        <option value="VPBank">
                        <option value="ACB (Á Châu)">
                        <option value="BIDV">
                        <option value="VietinBank">
                        <option value="TPBank">
                        <option value="Agribank">
                        <option value="Sacombank">
                        <option value="HDBank">
                        <option value="VIB">
                    </datalist>
                    @error('bank_name')<div style="color:red; font-size:0.85rem; margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label" style="font-size:0.88rem;">Tên chủ tài khoản (In hoa không dấu) <span style="color:red">*</span></label>
                    <input type="text" name="bank_account_holder" class="form-control" placeholder="VD: NGUYEN VAN AN" value="{{ old('bank_account_holder') }}" style="text-transform:uppercase;" required>
                    @error('bank_account_holder')<div style="color:red; font-size:0.85rem; margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-size:0.88rem;">Số tài khoản ngân hàng <span style="color:red">*</span></label>
                    <input type="text" name="bank_account_number" class="form-control" placeholder="VD: 0123456789" value="{{ old('bank_account_number') }}" required>
                    @error('bank_account_number')<div style="color:red; font-size:0.85rem; margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="font-size:0.78rem; color:#64748b; margin-top:0.75rem; display:flex; align-items:flex-start; gap:6px;">
                    <i class="fa-solid fa-shield-halved" style="color:#10b981; margin-top:2px;"></i>
                    <span>Thông tin ngân hàng chỉ sử dụng duy nhất cho mục đích giải ngân tiền hoàn theo đơn hàng và được bảo mật tuyệt đối.</span>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-paper-plane" style="margin-right:6px;"></i> Gửi yêu cầu hoàn hàng & nhận tiền
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('return_image_input');
    const dropzone = document.getElementById('upload_dropzone');
    const previewCard = document.getElementById('upload_preview_card');
    const previewImg = document.getElementById('preview_img');
    const previewFilename = document.getElementById('preview_filename');
    const previewFilesize = document.getElementById('preview_filesize');
    const btnRemove = document.getElementById('btn_remove_image');
    const errorBox = document.getElementById('client_upload_error');

    function showError(msg) {
        if (!errorBox) return;
        errorBox.textContent = msg;
        errorBox.style.display = 'block';
    }

    function clearError() {
        if (!errorBox) return;
        errorBox.textContent = '';
        errorBox.style.display = 'none';
    }

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function resetUpload() {
        fileInput.value = '';
        previewImg.src = '';
        previewFilename.textContent = '';
        previewFilesize.textContent = '';
        previewCard.style.display = 'none';
        dropzone.style.display = 'block';
        clearError();
    }

    function handleFile(file) {
        clearError();
        if (!file) return;

        // 1. Kiểm tra loại tệp
        const validExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        const fileExt = file.name.split('.').pop().toLowerCase();
        const isValidExt = validExtensions.includes(fileExt);
        const isValidMime = file.type.startsWith('image/') && ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);

        if (!isValidExt || !isValidMime) {
            showError('Định dạng tệp không hợp lệ. Vui lòng chọn tệp ảnh JPEG, PNG hoặc WebP.');
            fileInput.value = '';
            previewCard.style.display = 'none';
            dropzone.style.display = 'block';
            return;
        }

        // 2. Kiểm tra dung lượng (tối đa 5MB)
        const maxBytes = 5 * 1024 * 1024;
        if (file.size > maxBytes) {
            showError('Dung lượng tệp (' + formatBytes(file.size) + ') vượt quá giới hạn cho phép 5MB.');
            fileInput.value = '';
            previewCard.style.display = 'none';
            dropzone.style.display = 'block';
            return;
        }

        // 3. Đọc và hiển thị ảnh xem trước
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewFilename.textContent = file.name;
            previewFilesize.textContent = formatBytes(file.size);
            dropzone.style.display = 'none';
            previewCard.style.display = 'block';
        };
        reader.onerror = function() {
            showError('Không thể đọc tệp hình ảnh vừa chọn. Vui lòng thử lại.');
            resetUpload();
        };
        reader.readAsDataURL(file);
    }

    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files.length > 0) {
            handleFile(e.target.files[0]);
        }
    });

    btnRemove.addEventListener('click', function(e) {
        e.stopPropagation();
        resetUpload();
    });

    // Hỗ trợ kéo thả file (Drag & Drop)
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = '#4f46e5';
            dropzone.style.background = '#eef2ff';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = '#cbd5e1';
            dropzone.style.background = '#f8fafc';
        }, false);
    });

    dropzone.addEventListener('drop', function(e) {
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const dtFiles = e.dataTransfer.files;
            // Gán DataTransfer vào file input nếu trình duyệt hỗ trợ
            try {
                fileInput.files = dtFiles;
            } catch (err) {
                console.warn('Browser does not allow programmatic files assignment', err);
            }
            handleFile(dtFiles[0]);
        }
    });
});
</script>
@endsection
