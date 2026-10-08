@extends('layouts.admin')
@section('title', 'Quản lý Nhập Kho')
@section('actions')
    <a href="{{ route('admin.goods_receipts.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Tạo phiếu nhập</a>
@endsection

@section('content')
<div class="card flush">
    <div class="table-responsive">
        <table class="data">
            <thead>
                <tr>
                    <th>Mã Phiếu</th>
                    <th>Nhà cung cấp</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                <tr>
                    <td><strong>{{ $receipt->receipt_code }}</strong></td>
                    <td>{{ $receipt->supplier->name ?? 'N/A' }}</td>
                    <td>{{ number_format($receipt->total_amount) }}đ</td>
                    <td>
                        @if($receipt->status === 'completed')
                            <span class="badge" style="background:var(--ok-soft);color:var(--ok)">Hoàn thành</span>
                        @elseif($receipt->status === 'draft')
                            <span class="badge" style="background:var(--warn-soft);color:var(--warn)">Nháp</span>
                        @else
                            <span class="badge" style="background:var(--danger-soft);color:var(--danger)">Hủy</span>
                        @endif
                    </td>
                    <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.goods_receipts.show', $receipt) }}" class="btn-sm btn-outline">Xem chi tiết</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-state"><i class="fa-solid fa-box-open"></i> Chưa có phiếu nhập nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $receipts->links() }}</div>
@endsection
