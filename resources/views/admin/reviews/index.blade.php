@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá')

@section('content')
<div class="topbar">
    <div>
        <div class="breadcrumb">Quản lý / Đánh giá</div>
        <h1>Đánh giá Sản phẩm</h1>
    </div>
</div>

<div class="content">
    @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <div class="card flush" style="padding:0;overflow:hidden;">
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Sản phẩm</th>
                        <th>Số sao</th>
                        <th>Nội dung</th>
                        <th>Ngày ĐG</th>
                        <th>Trạng thái</th>
                        <th style="text-align:right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td>
                            <strong>{{ $review->user->name ?? 'N/A' }}</strong>
                            @if(!empty($review->user->email))
                                <div style="font-size:0.75rem;color:var(--muted);">{{ $review->user->email }}</div>
                            @endif
                        </td>
                        <td>
                            @if($review->product)
                                <a href="{{ route('admin.products.edit', $review->product->id) }}" style="color:var(--primary);font-weight:600;">
                                    {{ $review->product->name }}
                                </a>
                            @else
                                <span style="color:var(--muted);">Sản phẩm không tồn tại</span>
                            @endif
                        </td>
                        <td style="color:#f59e0b;white-space:nowrap;">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                            @endfor
                        </td>
                        <td style="max-width:280px;line-height:1.4;font-size:0.85rem;color:var(--ink-soft);">
                            {{ $review->comment }}
                        </td>
                        <td style="color:var(--muted);font-size:0.8rem;white-space:nowrap;">
                            {{ $review->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            @if($review->is_visible)
                                <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">
                                    <i class="fa-solid fa-check" style="margin-right:4px;"></i> Hiển thị
                                </span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#64748b;border:1px solid #cbd5e1;">
                                    <i class="fa-solid fa-eye-slash" style="margin-right:4px;"></i> Đã ẩn
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;">
                                <form action="{{ route('admin.reviews.toggle', $review->id) }}" method="POST" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline" title="{{ $review->is_visible ? 'Ẩn đánh giá' : 'Hiển thị đánh giá' }}">
                                        <i class="fa-solid {{ $review->is_visible ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        {{ $review->is_visible ? 'Ẩn' : 'Hiện' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Xóa đánh giá này vĩnh viễn?');" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2.5rem;color:var(--muted);">
                            <i class="fa-regular fa-comment-dots" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
                            Chưa có đánh giá nào từ khách hàng.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="margin-top:1.25rem;">
        {{ $reviews->links('vendor.pagination.admin') }}
    </div>
</div>
@endsection
