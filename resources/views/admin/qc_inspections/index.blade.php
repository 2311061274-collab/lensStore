@extends('layouts.admin')
@section('title', 'Kiểm định Hàng Hoàn (QC)')

@section('content')
<div class="grid-2">
    <!-- Cột bên trái: Danh sách đang chờ -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1rem;"><i class="fa-solid fa-boxes-packing text-primary"></i> Hàng mới trả về (Chờ QC)</h3>
        <p class="text-muted" style="font-size: 0.85rem; margin-bottom: 1rem;">Đây là danh sách các đơn khách vừa trả về kho, cần mở hộp kiểm tra trước khi nhập kho.</p>
        
        <div class="table-responsive">
            <table class="data">
                <thead>
                    <tr>
                        <th>Mã Y/C</th>
                        <th>Khách hàng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingReturns as $return)
                    <tr>
                        <td><strong>#RTN-{{ $return->id }}</strong></td>
                        <td>{{ $return->user->name ?? 'Khách lẻ' }}</td>
                        <td>
                            <a href="{{ route('admin.qc_inspections.create', ['return_request_id' => $return->id]) }}" class="btn-sm btn-primary">
                                Bắt đầu QC
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Không có hàng chờ kiểm định.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cột bên phải: Lịch sử QC -->
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1rem;"><i class="fa-solid fa-clipboard-check text-ok"></i> Lịch sử đã kiểm định</h3>
        
        <div class="table-responsive">
            <table class="data">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Tình trạng</th>
                        <th>Phân luồng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $qc)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--primary);">{{ $qc->product->name ?? 'N/A' }}</div>
                            <div style="font-size: 0.75rem; color: var(--muted);">Yêu cầu: #RTN-{{ $qc->return_request_id }}</div>
                        </td>
                        <td>
                            @if($qc->condition === 'perfect')
                                <span class="badge" style="background:var(--ok-soft);color:var(--ok)">Nguyên vẹn</span>
                            @elseif($qc->condition === 'scratched')
                                <span class="badge" style="background:var(--warn-soft);color:var(--warn)">Trầy xước</span>
                            @else
                                <span class="badge" style="background:var(--danger-soft);color:var(--danger)">Hư hỏng/Cũ</span>
                            @endif
                        </td>
                        <td>
                            @if($qc->final_action === 'restock')
                                <span style="font-weight: 600; color: var(--ok);"><i class="fa-solid fa-arrow-turn-down"></i> Bán lại</span>
                            @elseif($qc->final_action === 'send_to_vendor')
                                <span style="font-weight: 600; color: var(--warn);"><i class="fa-solid fa-industry"></i> Gửi hãng</span>
                            @else
                                <span style="font-weight: 600; color: var(--danger);"><i class="fa-solid fa-gavel"></i> Thanh lý</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Chưa có lịch sử.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $inspections->links() }}</div>
    </div>
</div>
@endsection
