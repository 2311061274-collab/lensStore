<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Danh sách các danh mục ống kính máy ảnh
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->paginate(10)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Form tạo danh mục mới
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Lưu danh mục mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục ống kính.',
            'name.unique' => 'Tên danh mục này đã tồn tại trong hệ thống.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Thêm danh mục ống kính thành công!');
    }

    /**
     * Chi tiết danh mục kèm danh sách ống kính thuộc danh mục
     */
    public function show(Category $category)
    {
        $category->load('products');
        return view('categories.show', compact('category'));
    }

    /**
     * Form chỉnh sửa danh mục
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục ống kính.',
            'name.unique' => 'Tên danh mục này đã tồn tại trong hệ thống.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Cập nhật danh mục thành công!');
    }

    /**
     * Xóa danh mục
     */
    public function destroy(Category $category)
    {
        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', "Đã xóa danh mục '{$categoryName}' thành công!");
    }
}