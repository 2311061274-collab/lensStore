@extends('layouts.admin')
@section('title', 'Thêm Bài viết mới')
@section('actions')
    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
@endsection
@section('content')

<div class="card">
    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="title">Tiêu đề bài viết *</label>
            <input type="text" name="title" id="title" class="form-control" required value="{{ old('title') }}">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="tag">Mã Tag (vd: review, tips, news)</label>
                <input type="text" name="tag" id="tag" class="form-control" required value="{{ old('tag') }}">
            </div>
            <div class="form-group">
                <label for="tag_text">Tên Tag hiển thị (vd: Review, Mẹo chụp ảnh)</label>
                <input type="text" name="tag_text" id="tag_text" class="form-control" required value="{{ old('tag_text') }}">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Mô tả ngắn gọn</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="content">Nội dung chi tiết *</label>
            <textarea name="content" id="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="image_upload">Tải Hình ảnh lên (Tùy chọn)</label>
                <input type="file" name="image_upload" id="image_upload" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label for="image_url">Hoặc nhập URL Hình ảnh</label>
                <input type="url" name="image_url" id="image_url" class="form-control" value="{{ old('image_url') }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="read_time">Thời gian đọc (phút)</label>
                <input type="number" name="read_time" id="read_time" class="form-control" value="{{ old('read_time', 5) }}">
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 30px;">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} style="width: 20px; height: 20px;">
                <label for="is_featured" style="margin: 0; font-weight: 600; cursor: pointer;">Đánh dấu là bài viết Nổi bật</label>
            </div>
        </div>

        <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Đăng bài viết</button>
        </div>
    </form>
</div>
@endsection
