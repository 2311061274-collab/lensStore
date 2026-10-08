@extends('layouts.admin')
@section('title', 'Quản lý Tin tức')
@section('actions')
    <a href="{{ route('admin.news.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm bài viết mới</a>
@endsection
@section('content')

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Chủ đề</th>
                    <th>Tác giả</th>
                    <th>Ngày tạo</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($news as $article)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ Str::limit($article->title, 50) }}</div>
                        @if($article->is_featured)
                        <span style="font-size: 0.7rem; background: var(--accent); color: white; padding: 2px 6px; border-radius: 4px; font-weight: bold;">Nổi bật</span>
                        @endif
                    </td>
                    <td><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 0.85rem;">{{ $article->tag_text }}</span></td>
                    <td>{{ $article->author }}</td>
                    <td>{{ $article->created_at->format('d/m/Y') }}</td>
                    <td style="text-align: right;">
                        <div class="action-buttons" style="justify-content: flex-end;">
                            <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-sm btn-secondary" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 3rem; color: var(--text-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
                        Chưa có bài viết nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($news->hasPages())
    <div style="margin-top: 1.5rem;">
        {{ $news->links('vendor.pagination.admin') }}
    </div>
    @endif
</div>
@endsection
