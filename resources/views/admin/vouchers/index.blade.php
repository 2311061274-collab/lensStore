@extends('layouts.admin')

@section('title', 'Quản lý Voucher')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fa-solid fa-ticket text-primary"></i> Quản lý Voucher</h2>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Thêm Voucher mới
    </a>
</div>

<div class="card mt-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã Voucher</th>
                    <th>Loại giảm giá</th>
                    <th>Giá trị</th>
                    <th>HSD</th>
                    <th>Lượt dùng</th>
                    <th>Trạng thái</th>
                    <th width="120">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vouchers as $v)
                    <tr>
                        <td><strong>{{ $v->code }}</strong></td>
                        <td>
                            @if($v->discount_type == 'fixed')
                                <span class="badge" style="background:#e0f2fe;color:#0369a1;padding:4px 8px;border-radius:4px;font-size:0.8rem;">Giảm thẳng</span>
                            @else
                                <span class="badge" style="background:#fce7f3;color:#be185d;padding:4px 8px;border-radius:4px;font-size:0.8rem;">Phần trăm</span>
                            @endif
                        </td>
                        <td>
                            @if($v->discount_type == 'fixed')
                                {{ number_format($v->discount_value, 0, ',', '.') }}đ
                            @else
                                {{ $v->discount_value }}%
                                @if($v->max_discount_value)
                                    <br><small class="text-muted">(Tối đa: {{ number_format($v->max_discount_value, 0, ',', '.') }}đ)</small>
                                @endif
                            @endif
                            @if($v->min_order_value > 0)
                                <br><small class="text-muted">Đơn từ: {{ number_format($v->min_order_value, 0, ',', '.') }}đ</small>
                            @endif
                        </td>
                        <td>
                            @if($v->starts_at) Từ: {{ $v->starts_at->format('d/m/Y') }}<br> @endif
                            @if($v->expires_at) Đến: {{ $v->expires_at->format('d/m/Y') }} @else Vô thời hạn @endif
                        </td>
                        <td>
                            {{ $v->used_count }} / {{ $v->usage_limit ?: '∞' }}
                        </td>
                        <td>
                            @if($v->is_active)
                                <span style="color:var(--success);"><i class="fa-solid fa-check-circle"></i> Hoạt động</span>
                            @else
                                <span style="color:var(--danger);"><i class="fa-solid fa-xmark-circle"></i> Tạm khóa</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.vouchers.edit', $v->id) }}" class="btn btn-secondary btn-sm" title="Sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.vouchers.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa voucher này?');" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fa-solid fa-ticket"></i>
                            <p>Chưa có voucher nào. Hãy tạo mới!</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $vouchers->links('vendor.pagination.admin') }}
    </div>
</div>
@endsection
