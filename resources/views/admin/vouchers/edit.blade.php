@extends('layouts.admin')

@section('title', 'Sửa Voucher')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2><i class="fa-solid fa-pen-to-square text-primary"></i> Sửa Voucher: {{ $voucher->code }}</h2>
    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card mt-4">
    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-4">
            <div class="form-group">
                <label>Mã Voucher <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $voucher->code) }}" required style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label>Loại giảm giá <span class="text-danger">*</span></label>
                <select name="discount_type" class="form-control" id="discount_type" required>
                    <option value="fixed" {{ old('discount_type', $voucher->discount_type) == 'fixed' ? 'selected' : '' }}>Giảm thẳng (VNĐ)</option>
                    <option value="percent" {{ old('discount_type', $voucher->discount_type) == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Giá trị giảm <span class="text-danger">*</span></label>
                <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value', $voucher->discount_value) }}" required min="0" step="0.01">
            </div>

            <div class="form-group" id="max_discount_wrapper" style="display: {{ old('discount_type', $voucher->discount_type) == 'percent' ? 'block' : 'none' }}">
                <label>Mức giảm tối đa (VNĐ)</label>
                <input type="number" name="max_discount_value" class="form-control" value="{{ old('max_discount_value', $voucher->max_discount_value) }}" min="0">
            </div>

            <div class="form-group">
                <label>Giá trị đơn tối thiểu (VNĐ)</label>
                <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value', $voucher->min_order_value) }}" min="0">
            </div>

            <div class="form-group">
                <label>Giới hạn lượt dùng</label>
                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $voucher->usage_limit) }}" min="1">
            </div>

            <div class="form-group">
                <label>Thời gian bắt đầu</label>
                <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $voucher->starts_at ? $voucher->starts_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="form-group">
                <label>Thời gian kết thúc</label>
                <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '') }}">
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="display:flex; align-items:center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <strong>Kích hoạt Voucher</strong>
                </label>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('discount_type').addEventListener('change', function() {
        if(this.value === 'percent') {
            document.getElementById('max_discount_wrapper').style.display = 'block';
        } else {
            document.getElementById('max_discount_wrapper').style.display = 'none';
        }
    });
</script>
@endsection
