@extends('layouts.admin')
@section('title', 'Tạo Phiếu Nhập Kho')
@section('actions')
    <a href="{{ route('admin.goods_receipts.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('admin.goods_receipts.store') }}" method="POST">
        @csrf
        
        <div class="grid-2">
            <div>
                <div class="form-group">
                    <label>Ghi chú / Lý do nhập</label>
                    <textarea name="note" rows="3" class="form-control" placeholder="Nhập từ lô hàng tháng 10..."></textarea>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label>Nhà cung cấp</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="supplier_id" class="form-control" style="flex: 1;">
                            <option value="">-- Chọn Nhà cung cấp có sẵn --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                        <span style="display: flex; align-items: center; color: var(--muted); font-weight: bold;">HOẶC</span>
                        <input type="text" name="new_supplier_name" class="form-control" placeholder="Tạo NCC mới..." style="flex: 1;">
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;" class="mt-4 mb-4">
            <h3 class="card-title" style="margin: 0;">Danh sách ống kính nhập</h3>
            <a href="{{ route('admin.products.create') }}" target="_blank" class="btn-sm btn-secondary">
                <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới (Mở tab mới)
            </a>
        </div>
        
        <div class="table-responsive" style="margin-bottom: 20px;">
            <table class="table" id="products-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng nhập</th>
                        <th>Giá vốn (1 SP)</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="product-rows">
                    <!-- Dòng mẫu 1 -->
                    <tr>
                        <td>
                            <select name="products[0][id]" class="form-control" required>
                                <option value="">-- Chọn Hãng / Ống kính --</option>
                                @foreach($productsByBrand as $brand => $products)
                                    @if($products->count() > 0)
                                        <optgroup label="{{ $brand ?: 'Khác' }}">
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} (Tồn: {{ $p->stock }})</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="products[0][quantity]" class="form-control" min="1" value="1" required>
                        </td>
                        <td>
                            <input type="number" name="products[0][unit_price]" class="form-control" min="0" value="0" required>
                        </td>
                        <td>
                            <button type="button" class="btn-sm btn-danger remove-row"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn-sm btn-secondary mt-4" id="add-row"><i class="fa-solid fa-plus"></i> Thêm sản phẩm khác</button>
        </div>

        <hr style="border:0; border-top:1px solid var(--line); margin: 2rem 0;">
        <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Lưu bản nháp (Chưa cộng kho)</button>
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
        
        // Đổi index của name attribute (vd products[0][id] thành products[1][id])
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
