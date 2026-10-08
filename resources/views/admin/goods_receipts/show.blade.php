@extends('layouts.admin')
@section('title', 'Chi tiết Phiếu Nhập Kho')
@section('actions')
    <a href="{{ route('admin.goods_receipts.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Danh sách</a>
@endsection

@push('styles')
<style>
    .receipt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 1.5rem;
        border-bottom: 1px dashed var(--line-strong);
        margin-bottom: 1.5rem;
    }
    .receipt-title {
        font-family: var(--display);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 0.3rem;
    }
    .receipt-meta {
        color: var(--muted);
        font-size: 0.85rem;
        font-weight: 500;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .info-box label {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        font-weight: 700;
        margin-bottom: 0.4rem;
    }
    .info-box div {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--ink);
    }
    
    .action-panel {
        background: var(--surface-soft);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 2rem;
    }
    .action-panel h4 {
        color: var(--ink);
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    .action-panel p {
        color: var(--muted);
        font-size: 0.85rem;
        margin-bottom: 1.25rem;
    }
    
    .table-totals {
        width: 100%;
        max-width: 350px;
        margin-left: auto;
        margin-top: 1rem;
        font-size: 0.95rem;
    }
    .table-totals td {
        padding: 0.5rem 0;
    }
    .table-totals tr.grand-total {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--danger);
        border-top: 2px solid var(--line);
    }
    .table-totals tr.grand-total td {
        padding-top: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="card" style="max-width: 1000px; margin: 0 auto;">
    
    <!-- HEADER -->
    <div class="receipt-header">
        <div>
            <div class="receipt-title">{{ $goodsReceipt->receipt_code }}</div>
            <div class="receipt-meta">Ngày lập: {{ $goodsReceipt->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div>
            @if($goodsReceipt->status === 'completed')
                <span class="badge" style="background:var(--ok-soft);color:var(--ok); padding: 0.5rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-circle-check mr-1"></i> Đã Duyệt & Nhập Kho</span>
            @elseif($goodsReceipt->status === 'draft')
                <span class="badge" style="background:var(--warn-soft);color:var(--warn); padding: 0.5rem 1rem; font-size: 0.85rem;"><i class="fa-solid fa-clock mr-1"></i> Bản Nháp (Chờ Duyệt)</span>
            @endif
        </div>
    </div>

    <!-- THÔNG TIN CHUNG -->
    <div class="info-grid">
        <div class="info-box">
            <label>Người lập phiếu</label>
            <div>
                <i class="fa-solid fa-user-circle" style="color:var(--muted)"></i> 
                {{ $goodsReceipt->user->name ?? 'N/A' }}
            </div>
        </div>
        <div class="info-box">
            <label>Nhà cung cấp</label>
            <div>
                <i class="fa-solid fa-building" style="color:var(--muted)"></i> 
                {{ $goodsReceipt->supplier->name ?? 'Không xác định' }}
            </div>
        </div>
        <div class="info-box">
            <label>Trạng thái tồn kho</label>
            <div>
                @if($goodsReceipt->status === 'completed')
                    <span style="color: var(--ok);"><i class="fa-solid fa-arrow-up-right-dots"></i> Đã cộng kho hệ thống</span>
                @else
                    <span style="color: var(--warn);"><i class="fa-solid fa-pause"></i> Chưa cộng vào hệ thống</span>
                @endif
            </div>
        </div>
        <div class="info-box">
            <label>Ghi chú</label>
            <div style="font-weight: 500; font-style: italic;">
                {{ $goodsReceipt->note ?: 'Không có ghi chú' }}
            </div>
        </div>
    </div>

    <!-- NẾU CHƯA DUYỆT THÌ HIỆN BOX HÀNH ĐỘNG -->
    @if($goodsReceipt->status === 'draft')
    <div class="action-panel">
        <h4>Xác nhận Nhập kho</h4>
        <p>Thao tác này sẽ tự động cập nhật số lượng tồn kho của các sản phẩm bên dưới lên hệ thống và ghi nhận vào Lịch sử kho. Không thể hoàn tác!</p>
        <form action="{{ route('admin.goods_receipts.complete', $goodsReceipt) }}" method="POST">
            @csrf
            <button type="submit" class="btn-success" style="padding: 0.75rem 2rem; font-size: 0.95rem; border-radius: 99px; box-shadow: 0 8px 16px rgba(16,185,129,0.3);" onclick="return confirm('Bạn có chắc chắn duyệt phiếu này? Hàng sẽ được đẩy vào kho ngay lập tức.')">
                <i class="fa-solid fa-clipboard-check"></i> DUYỆT PHIẾU & CỘNG KHO
            </button>
        </form>
    </div>
    @endif

    <!-- BẢNG CHI TIẾT SẢN PHẨM -->
    <h3 style="font-family: var(--display); font-size: 1.1rem; margin-bottom: 1rem;">Chi tiết lô hàng nhập</h3>
    <div class="table-wrap mb-4">
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">STT</th>
                    <th>Sản phẩm / Ống kính</th>
                    <th style="text-align: center;">Số lượng</th>
                    <th style="text-align: right;">Đơn giá nhập</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goodsReceipt->details as $index => $detail)
                <tr>
                    <td style="text-align: center; color: var(--muted); font-weight: 600;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: var(--primary);">{{ $detail->product->name ?? 'Sản phẩm đã bị xóa' }}</strong>
                    </td>
                    <td style="text-align: center;">
                        <span style="background: var(--surface-soft); padding: 4px 12px; border-radius: 6px; font-weight: 700;">
                            {{ $detail->quantity }}
                        </span>
                    </td>
                    <td style="text-align: right;">{{ number_format($detail->unit_price) }} ₫</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($detail->total_price) }} ₫</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- TỔNG TIỀN -->
    <table class="table-totals">
        <tr>
            <td style="color: var(--muted); text-align: right;">Tổng số lượng SP:</td>
            <td style="text-align: right; width: 120px; font-weight: 700;">{{ $goodsReceipt->details->sum('quantity') }}</td>
        </tr>
        <tr class="grand-total">
            <td style="text-align: right;">TỔNG TIỀN NHẬP:</td>
            <td style="text-align: right;">{{ number_format($goodsReceipt->total_amount) }} ₫</td>
        </tr>
    </table>

</div>
@endsection
