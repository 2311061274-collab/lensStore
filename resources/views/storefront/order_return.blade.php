@extends('layouts.app')

@section('content')
<style>
    .return-container { max-width: 680px; margin: 3rem auto; padding: 0 1rem; }
    .card { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); padding: 2.2rem; border: 1px solid #e2e8f0; }
    .card-title { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
    .order-badge-bar { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 700; color: #334155; margin-bottom: 0.5rem; font-size: 0.95rem; }
    .form-control { width: 100%; padding: 0.8rem 1rem; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; transition: border-color 0.2s, box-shadow 0.2s; }
    .form-control:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }
    .btn-submit { background: #4f46e5; color: white; border: none; padding: 0.95rem 2rem; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-submit:hover:not(:disabled) { background: #4338ca; }
    .btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }

    /* Guide Box */
    .drive-guide-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 1.1rem;
        margin-top: 0.6rem;
        font-size: 0.88rem;
        color: #166534;
        line-height: 1.55;
    }
    .drive-guide-card ol {
        margin: 0.5rem 0 0.5rem 1.25rem;
        padding: 0;
    }
    .drive-guide-card li {
        margin-bottom: 0.35rem;
    }
</style>

<div class="return-container">
    <div class="card">
        <h2 class="card-title"><i class="fa-solid fa-rotate-left" style="color:#4f46e5; margin-right:6px;"></i> Yêu cầu Trả hàng / Hoàn tiền</h2>
        <p style="color:#64748b; margin-bottom: 1.25rem; font-size: 0.95rem;">
            Vui lòng điền thông tin và liên kết video mở hàng Google Drive để LensStore tiếp nhận và xử lý nhanh chóng.
        </p>

        {{-- Thông tin tóm tắt đơn hàng gốc & sản phẩm --}}
        <div class="order-badge-bar">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
                <div>
                    <span style="font-size:0.8rem; color:#64748b; text-transform:uppercase; font-weight:700;">Đơn hàng gốc</span>
                    <div style="font-size:1.1rem; font-weight:800; color:#1e293b;">
                        #{{ $order->order_code ?? str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:0.8rem; color:#64748b;">Tổng tiền hoàn</span>
                    <div style="font-size:1.15rem; font-weight:800; color:#ef4444;">
                        {{ number_format($order->total, 0, ',', '.') }} ₫
                    </div>
                </div>
            </div>

            <div style="border-top:1px dashed #cbd5e1; padding-top:0.75rem;">
                <div style="font-size:0.82rem; font-weight:700; color:#475569; margin-bottom:6px;">Sản phẩm trong đơn:</div>
                @foreach($order->items as $item)
                    <div style="display:flex; align-items:center; justify-content:space-between; font-size:0.88rem; margin-bottom:4px;">
                        <span style="color:#1e293b;">• {{ $item->product_name }}</span>
                        <span style="color:#64748b; font-weight:600;">SL: {{ $item->quantity }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if(session('error'))
            <div style="background:#fee2e2;color:#991b1b;padding:1rem;border-radius:8px;margin-bottom:1rem;border:1px solid #fca5a5;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <form id="returnRequestForm" action="{{ route('orders.return.store', $order->id) }}" method="POST">
            @csrf
            
            {{-- Lý do trả hàng --}}
            <div class="form-group">
                <label class="form-label">Lý do trả hàng <span style="color:red">*</span></label>
                <select name="reason" class="form-control" required>
                    <option value="">-- Chọn lý do --</option>
                    <option value="Hàng lỗi, không hoạt động" {{ old('reason') == 'Hàng lỗi, không hoạt động' ? 'selected' : '' }}>Hàng lỗi quang học / không hoạt động</option>
                    <option value="Giao sai mẫu mã, màu sắc" {{ old('reason') == 'Giao sai mẫu mã, màu sắc' ? 'selected' : '' }}>Giao sai mẫu mã, ngàm hoặc tiêu cự</option>
                    <option value="Thiếu phụ kiện, linh kiện" {{ old('reason') == 'Thiếu phụ kiện, linh kiện' ? 'selected' : '' }}>Thiếu phụ kiện, nắp lens hoặc hood</option>
                    <option value="Hàng giả, hàng nhái" {{ old('reason') == 'Hàng giả, hàng nhái' ? 'selected' : '' }}>Nghi vấn hàng giả, hàng nhái</option>
                    <option value="Hư hỏng trong quá trình vận chuyển" {{ old('reason') == 'Hư hỏng trong quá trình vận chuyển' ? 'selected' : '' }}>Hộp móp méo, trầy xước do vận chuyển</option>
                    <option value="Lý do khác" {{ old('reason') == 'Lý do khác' ? 'selected' : '' }}>Lý do khác</option>
                </select>
                @error('reason')<div style="color:red; font-size:0.85rem; margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            {{-- Video mở hàng trên Google Drive (BẮT BUỘC theo yêu cầu) --}}
            <div class="form-group">
                <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                    <span><i class="fa-brands fa-google-drive" style="color:#0f9d58; margin-right:4px;"></i> Liên kết Video mở hàng (Google Drive) <span style="color:red">*</span></span>
                    <span style="font-size:0.8rem; font-weight:400; color:#64748b;">Không tải trực tiếp tệp</span>
                </label>
                
                <input type="url" 
                       id="video_url_input" 
                       name="video_url" 
                       class="form-control" 
                       placeholder="https://drive.google.com/file/d/.../view?usp=sharing" 
                       value="{{ old('video_url') }}" 
                       required>
                
                <div id="drive_url_feedback" style="display:none; font-size:0.85rem; margin-top:6px; font-weight:600;"></div>

                @error('video_url')
                    <div style="color:red; font-size:0.85rem; margin-top:5px;">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </div>
                @enderror

                {{-- Hướng dẫn chi tiết chia sẻ Google Drive --}}
                <div class="drive-guide-card">
                    <div style="font-weight:700; display:flex; align-items:center; gap:6px;">
                        <i class="fa-solid fa-circle-info"></i> Hướng dẫn lấy liên kết video mở hàng từ Google Drive:
                    </div>
                    <ol>
                        <li>Tải video quay rõ quá trình bóc hộp kiện hàng và số seri ống kính lên <strong>Google Drive</strong> của bạn.</li>
                        <li>Nhấp chuột phải vào video (hoặc nhấn nút 3 chấm trên điện thoại) &rarr; Chọn <strong>Chia sẻ (Share)</strong>.</li>
                        <li>Tại mục <strong>Quyền truy cập chung (General access)</strong>, chuyển sang: <strong>"Bất kỳ ai có đường liên kết (Anyone with the link)"</strong> với quyền <strong>Người xem (Viewer)</strong>.</li>
                        <li>Bấm <strong>Sao chép đường liên kết (Copy link)</strong> và dán vào ô nhập ở trên.</li>
                    </ol>
                    <div style="font-size:0.8rem; color:#15803d; border-top:1px dashed #86efac; padding-top:0.4rem; margin-top:0.4rem;">
                        <i class="fa-solid fa-shield-halved"></i> <em>Lưu ý: Bạn không cần đăng công khai rộng rãi trên mạng xã hội, chỉ cần cấp quyền xem cho người có link để Admin LensStore có thể truy cập đối chiếu trước khi duyệt hoàn hàng.</em>
                    </div>
                </div>
            </div>

            {{-- Ghi chú chi tiết --}}
            <div class="form-group">
                <label class="form-label">Ghi chú chi tiết tình trạng</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Mô tả cụ thể khuyết tật hoặc vấn đề gặp phải...">{{ old('note') }}</textarea>
            </div>

            {{-- Thông tin tài khoản nhận tiền hoàn --}}
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:1.25rem; margin-bottom:1.5rem;">
                <div style="font-weight:700; color:#1e293b; margin-bottom:0.75rem; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-building-columns" style="color:#4f46e5;"></i> Thông tin tài khoản ngân hàng nhận tiền hoàn
                </div>
                <div style="font-size:0.85rem; color:#64748b; margin-bottom:1rem; line-height:1.5;">
                    LensStore sẽ giải ngân hoàn tiền chuyển khoản sau khi tiếp nhận kiện hàng và kiểm định chất lượng (QC) đạt điều kiện.
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
            </div>

            <button type="submit" id="btnSubmitReturn" class="btn-submit">
                <i class="fa-solid fa-paper-plane"></i> <span id="btnSubmitText">Gửi yêu cầu trả hàng & hoàn tiền</span>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoInput = document.getElementById('video_url_input');
    const feedback = document.getElementById('drive_url_feedback');
    const form = document.getElementById('returnRequestForm');
    const btnSubmit = document.getElementById('btnSubmitReturn');
    const btnText = document.getElementById('btnSubmitText');

    function checkDriveUrl(url) {
        if (!url) {
            feedback.style.display = 'none';
            return;
        }

        const isDrive = url.includes('drive.google.com') || url.includes('docs.google.com');
        if (isDrive) {
            feedback.style.display = 'block';
            feedback.style.color = '#15803d';
            feedback.innerHTML = '<i class="fa-solid fa-circle-check"></i> Liên kết Google Drive hợp lệ. Vui lòng đảm bảo quyền xem đã được mở.';
        } else {
            feedback.style.display = 'block';
            feedback.style.color = '#dc2626';
            feedback.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Vui lòng nhập link thuộc tên miền drive.google.com';
        }
    }

    if (videoInput) {
        videoInput.addEventListener('input', function() {
            checkDriveUrl(this.value.trim());
        });
        if (videoInput.value) {
            checkDriveUrl(videoInput.value.trim());
        }
    }

    // Chống nhấn đúp (double submit) hoặc gửi trùng
    if (form) {
        form.addEventListener('submit', function(e) {
            if (btnSubmit.disabled) {
                e.preventDefault();
                return false;
            }

            const urlVal = (videoInput.value || '').trim();
            if (!urlVal.includes('drive.google.com') && !urlVal.includes('docs.google.com')) {
                e.preventDefault();
                alert('Vui lòng cung cấp liên kết video từ Google Drive hợp lệ.');
                videoInput.focus();
                return false;
            }

            // Vô hiệu hóa nút và hiện trạng thái đang gửi
            btnSubmit.disabled = true;
            btnText.textContent = 'Đang gửi yêu cầu...';
        });
    }
});
</script>
@endsection
