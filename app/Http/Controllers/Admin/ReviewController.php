<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product', 'order'])->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleVisibility(Review $review)
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        return back()->with('success', 'Đã thay đổi trạng thái hiển thị của đánh giá.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
