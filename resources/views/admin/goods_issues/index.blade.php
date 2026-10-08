@extends('layouts.admin')
@section('title', 'Danh sách Phiếu Xuất Kho')
@section('actions')
    <a href="{{ route('admin.goods_issues.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Tạo Phiếu Xuất (Thủ công)</a>
@endsection

@section('content')
<div class="card flush">
    <div class="table-responsive">
        <table class="data">
            <thead>
                <tr>
                    <th>Mã Phiếu</th>
                    <th>Loại xuất</th>
                    <th>Tham chiếu</th>
                    <th>Trạng thái</th>
                    <th>Ngày xuất</th>
                </tr>
            </thead>
            <tbody>
                @forelse($issues as $issue)
                <tr>
                    <td><strong>{{ $issue->issue_code }}</strong></td>
                    <td>
                        @if($issue->type === 'sale')
                            <span class="badge" style="background:#e0f2fe;color:#0284c7">Xuất Bán Hàng</span>
                        @elseif($issue->type === 'warranty')
                            <span class="badge" style="background:#fef08a;color:#ca8a04">Xuất Bảo Hành</span>
                        @elseif($issue->type === 'destroy')
                            <span class="badge" style="background:#fecdd3;color:#e11d48">Xuất Tiêu Hủy</span>
                        @else
                            <span class="badge" style="background:#f3f4f6;color:#4b5563">Xuất Khác</span>
                        @endif
                    </td>
                    <td>
                        @if($issue->order_id)
                            <a href="{{ route('admin.orders.show', $issue->order_id) }}">Đơn hàng #{{ $issue->order_id }}</a>
                        @else
                            {{ $issue->note ?? 'Không' }}
                        @endif
                    </td>
                    <td>
                        @if($issue->status === 'completed')
                            <span class="text-ok"><i class="fa-solid fa-check"></i> Đã xuất</span>
                        @else
                            <span class="text-muted"><i class="fa-solid fa-clock"></i> {{ $issue->status }}</span>
                        @endif
                    </td>
                    <td>{{ $issue->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state"><i class="fa-solid fa-box-open"></i> Chưa có phiếu xuất kho nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $issues->links() }}</div>
@endsection
