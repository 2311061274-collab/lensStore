@extends('layouts.admin')

@section('title', 'Thêm Danh Mục Ống Kính')

@section('content')
<h1>Thêm Danh Mục Ống Kính Mới</h1>

<p>
    <a href="{{ route('admin.categories.index') }}">&larr; Quay lại danh sách danh mục</a>
</p>

<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="name">Tên Danh Mục: <span style="color:red;">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Ống kính Prime, Ống kính Zoom..." required>
        @error('name')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="description">Mô Tả:</label>
        <textarea id="description" name="description" rows="4" placeholder="Nhập mô tả ngắn về danh mục này...">{{ old('description') }}</textarea>
        @error('description')
            <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <button type="submit" class="btn">Lưu Danh Mục</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy bỏ</a>
    </div>
</form>
@endsection
