@extends('layouts.admin')
@section('title', 'Tạo Phiếu Xuất Kho (Thủ công)')
@section('actions')
    <a href="{{ route('admin.goods_issues.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
@endsection

@section('content')
<div class="card">
    <div class="alert alert-info mb-4">
        <i class="fa-solid fa-circle-info"></i> Lưu ý: Phiếu xuất hàng cho Đơn Hàng (Sale) được tự động sinh ra khi đơn hàng hoàn tất. Chức năng này dùng để Xuất bảo hành, Xuất tiêu hủy (hàng lỗi) hoặc mục đích khác.
    </div>

    <form action="{{ route('admin.goods_issues.store') }}" method="POST">
        @csrf
        
        <div class="grid-2">
            <div>
                <div class="form-group">
                    <label>Loại Phiếu Xuất</label>
                    <select name="type" class="form-control" required>
                        <option value="warranty">Xuất Bảo Hành / Sửa chữa</option>
                        <option value="destroy">Xuất Tiêu Hủy (Hàng hỏng)</option>
                        <option value="other">Xuất mục đích khác</option>
                    </select>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label>Ghi chú lý do</label>
                    <textarea name="note" rows="2" class="form-control" placeholder="Ghi chú chi tiết lý do xuất..."></textarea>
                </div>
            </div>
        </div>

        <h3 class="card-title mt-4 mb-4">Danh sách sản phẩm xuất</h3>
        
        <div class="table-responsive" style="margin-bottom: 20px;">
            <table class="table" id="products-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="width: 200px">Số lượng xuất</th>
                        <th style="width: 80px">Xóa</th>
                    </tr>
                </thead>
                <tbody id="product-rows">
                    <tr>
                        <td>
                            <select name="products[0][id]" class="form-control" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Tồn thực tế: {{ $p->stock }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="products[0][quantity]" class="form-control" min="1" value="1" required>
                        </td>
                        <td>
                            <button type="button" class="btn-sm btn-danger remove-row"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn-sm btn-secondary mt-4" id="add-row"><i class="fa-solid fa-plus"></i> Thêm sản phẩm</button>
        </div>

        <hr style="border:0; border-top:1px solid var(--line); margin: 2rem 0;">
        <button type="submit" class="btn-primary" onclick="return confirm('Sẽ trừ trực tiếp vào kho thực tế, bạn có chắc chắn?')">
            <i class="fa-solid fa-check"></i> XÁC NHẬN XUẤT KHO
        </button>
    </form>
</div>
@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIdx = 1;
    document.getElementById('add-row').addEventListener('click', function() {
        const tbody = document.getElementById('product-rows');
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        newRow.innerHTML = newRow.innerHTML.replace(/products\[0\]/g, `products[${rowIdx}]`);
        tbody.appendChild(newRow);
        rowIdx++;
    });

    document.getElementById('product-rows').addEventListener('click', function(e) {
        if(e.target.closest('.remove-row')) {
            if(document.querySelectorAll('#product-rows tr').length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('Phải có ít nhất 1 sản phẩm!');
            }
        }
    });
});
</script>
