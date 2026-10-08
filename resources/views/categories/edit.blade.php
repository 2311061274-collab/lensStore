@extends('layouts.admin')

@section('title', 'Sửa Danh Mục: ' . $category->name)

@section('actions')
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>
@endsection

@push('styles')
<style>
    .cat-edit-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.5rem;
        align-items: start;
    }
    .section-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .section-header {
        padding: 1rem 1.35rem;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        gap: .6rem;
        background: var(--surface-soft);
    }
    .section-header i { color: var(--primary); font-size: .9rem; }
    .section-header h3 { font-size: .88rem; font-weight: 700; color: var(--ink); }
    .section-body { padding: 1.35rem; display: flex; flex-direction: column; gap: 1rem; }

    .field label {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--muted);
        margin-bottom: .4rem;
    }
    .field label .req { color: var(--danger); }
    .field input, .field textarea {
        width: 100%;
        padding: .6rem .85rem;
        border: 1.5px solid var(--line);
        border-radius: 10px;
        font-size: .88rem;
        color: var(--ink);
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
        font-family: inherit;
    }
    .field input:focus, .field textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    }
    .field textarea { resize: vertical; min-height: 130px; }
    .field .error-text { font-size: .75rem; color: var(--danger); margin-top: .3rem; font-weight: 500; }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .75rem;
        padding: 1rem 1.35rem;
        background: var(--surface-soft);
        border-top: 1px solid var(--line);
    }

    /* Info side card */
    .info-list { list-style: none; display: flex; flex-direction: column; gap: .75rem; }
    .info-list li {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
        font-size: .82rem;
        color: var(--ink-soft);
        line-height: 1.5;
    }
    .info-list li i { color: var(--primary); margin-top: .15rem; font-size: .8rem; flex-shrink: 0; }

    .stat-row { display: flex; gap: .75rem; }
    .mini-stat {
        flex: 1;
        background: var(--surface-soft);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: .85rem 1rem;
        text-align: center;
    }
    .mini-stat .num { font-size: 1.4rem; font-weight: 800; color: var(--primary); line-height: 1; }
    .mini-stat .lbl { font-size: .7rem; font-weight: 600; color: var(--muted); margin-top: .2rem; text-transform: uppercase; }

    @media(max-width:768px) { .cat-edit-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

@if($errors->any())
<div class="alert alert-error" style="margin-bottom:1rem;">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <strong>Có lỗi xảy ra:</strong> Vui lòng kiểm tra lại các trường bắt buộc.
</div>
@endif

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1rem;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="cat-edit-layout">
        {{-- LEFT: Form --}}
        <div>
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-folder-pen"></i>
                    <h3>Thông tin danh mục</h3>
                </div>
                <div class="section-body">
                    <div class="field">
                        <label>Tên danh mục <span class="req">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" required placeholder="VD: Ống kính Góc rộng, Portrait...">
                        @error('name')<div class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Mô tả danh mục</label>
                        <textarea name="description" rows="5" placeholder="Mô tả ngắn gọn về danh mục này, loại ống kính, ứng dụng...">{{ old('description', $category->description) }}</textarea>
                        @error('description')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-xmark"></i> Hủy bỏ
                    </a>
                    <button type="submit" class="btn">
                        <i class="fa-solid fa-floppy-disk"></i> Cập nhật danh mục
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Info --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Thống kê nhanh --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-chart-bar"></i>
                    <h3>Thống kê</h3>
                </div>
                <div class="section-body">
                    <div class="stat-row">
                        <div class="mini-stat">
                            <div class="num">{{ $category->products_count ?? $category->products()->count() }}</div>
                            <div class="lbl">Sản phẩm</div>
                        </div>
                        <div class="mini-stat">
                            <div class="num">{{ $category->created_at->format('Y') }}</div>
                            <div class="lbl">Năm tạo</div>
                        </div>
                    </div>
                    <div style="font-size:.78rem;color:var(--muted);display:flex;align-items:center;gap:.4rem;">
                        <i class="fa-solid fa-clock"></i>
                        Cập nhật lần cuối: {{ $category->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            {{-- Lưu ý --}}
            <div class="section-card">
                <div class="section-header">
                    <i class="fa-solid fa-circle-info"></i>
                    <h3>Lưu ý quan trọng</h3>
                </div>
                <div class="section-body">
                    <ul class="info-list">
                        <li>
                            <i class="fa-solid fa-chevron-right"></i>
                            <span>Tên danh mục sẽ hiển thị trực tiếp ngoài trang cửa hàng trong bộ lọc sản phẩm.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-chevron-right"></i>
                            <span>Danh mục đang chứa sản phẩm sẽ không thể bị xóa cho đến khi chuyển hoặc xóa hết sản phẩm.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-chevron-right"></i>
                            <span>Mô tả nên ngắn gọn, đủ ý, giúp khách hàng hiểu rõ loại ống kính thuộc danh mục này.</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection
