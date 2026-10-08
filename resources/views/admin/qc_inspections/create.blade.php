@extends('layouts.admin')
@section('title', 'Thực hiện Kiểm Định (QC)')
@section('actions')
    <a href="{{ route('admin.qc_inspections.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
@endsection

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h3 class="card-title mb-4">Mở hộp & Đánh giá Yêu cầu Trả hàng #RTN-{{ $returnRequest->id }}</h3>
    
    <div style="background: var(--surface-soft); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
        <h4 style="font-size: 0.9rem; color: var(--muted); text-transform: uppercase; margin-bottom: 1rem;">Thông tin hàng hóa bên trong:</h4>
        <ul style="list-style: none; padding: 0;">
            @foreach($returnRequest->order->items as $item)
            <li style="margin-bottom: 0.5rem; font-weight: 600; font-size: 1.1rem; color: var(--primary);">
                <i class="fa-solid fa-camera mr-2"></i> {{ $item->product->name ?? 'Sản phẩm đã xóa' }} (Số lượng: {{ $item->quantity }})
            </li>
            @endforeach
        </ul>
        <div style="margin-top: 1rem; font-size: 0.85rem; color: var(--danger);">
            <strong>* Lý do khách trả:</strong> {{ $returnRequest->reason }}
        </div>
    </div>

    <form action="{{ route('admin.qc_inspections.store') }}" method="POST">
        @csrf
        <input type="hidden" name="return_request_id" value="{{ $returnRequest->id }}">
        
        <!-- Giả định QC cho sản phẩm đầu tiên trong đơn -->
        <input type="hidden" name="product_id" value="{{ $returnRequest->order->items->first()->product_id }}">
        <input type="hidden" name="quantity" value="{{ $returnRequest->order->items->first()->quantity }}">

        <div class="form-group mb-4">
            <label style="font-size: 0.9rem; color: var(--ink);">1. Tình trạng thực tế (Sau khi mở hộp)</label>
            <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                <label style="flex:1; background: var(--surface-soft); padding: 1rem; border: 1px solid var(--line); border-radius: var(--radius); text-align: center; cursor: pointer;">
                    <input type="radio" name="condition" value="perfect" required> 
                    <div style="margin-top: 0.5rem; font-weight: 600; color: var(--ok);">Nguyên vẹn / Nguyên seal</div>
                </label>
                <label style="flex:1; background: var(--surface-soft); padding: 1rem; border: 1px solid var(--line); border-radius: var(--radius); text-align: center; cursor: pointer;">
                    <input type="radio" name="condition" value="scratched" required> 
                    <div style="margin-top: 0.5rem; font-weight: 600; color: var(--warn);">Trầy xước / Móp vỏ</div>
                </label>
                <label style="flex:1; background: var(--surface-soft); padding: 1rem; border: 1px solid var(--line); border-radius: var(--radius); text-align: center; cursor: pointer;">
                    <input type="radio" name="condition" value="broken" required> 
                    <div style="margin-top: 0.5rem; font-weight: 600; color: var(--danger);">Hư hỏng nặng / Lỗi SX</div>
                </label>
            </div>
        </div>

        <div class="form-group mb-4">
            <label style="font-size: 0.9rem; color: var(--ink);">2. Quyết định phân luồng tồn kho</label>
            <select name="final_action" class="form-control" style="width: 100%; font-size: 1rem; padding: 0.75rem;" required>
                <option value="">-- Chọn hướng giải quyết --</option>
                <option value="restock">Nhập lại vào [Kho Bán Được] (Bán tiếp cho khách khác)</option>
                <option value="send_to_vendor">Chuyển vào [Kho Hàng Lỗi] (Chờ gom gửi hãng bảo hành)</option>
                <option value="liquidate">Chuyển vào [Kho Hàng Cũ] (Để xả kho thanh lý)</option>
            </select>
        </div>

        <div class="form-group mb-4">
            <label style="font-size: 0.9rem; color: var(--ink);">3. Ghi chú của thủ kho</label>
            <textarea name="inspection_note" class="form-control" rows="3" placeholder="Ví dụ: Vỏ hộp bị rách một góc, nhưng ống kính vẫn mới 100%..."></textarea>
        </div>

        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1.1rem; border-radius: 99px;">
            <i class="fa-solid fa-stamp"></i> XÁC NHẬN KIỂM ĐỊNH & PHÂN LUỒNG KHO
        </button>
    </form>
</div>
@endsection
