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
                <input type="file" name="image" class="form-control" accept="image/*">
                <div style="font-size:0.8rem; color:#94a3b8; margin-top:5px;">Tối đa 5MB. Định dạng JPG, PNG.</div>
                @error('image')<div style="color:red; font-size:0.85rem; margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Ghi chú chi tiết</label>
                <textarea name="note" class="form-control" rows="4" placeholder="Mô tả chi tiết tình trạng hàng hóa..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Gửi yêu cầu</button>
        </form>
    </div>
</div>
@endsection
