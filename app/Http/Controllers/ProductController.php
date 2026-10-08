<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm ống kính máy ảnh (Tìm kiếm, lọc danh mục, phân trang)
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Tìm kiếm theo từ khóa (Tên, SKU, Ngàm, Tiêu cự)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('mount', 'like', "%{$search}%")
                  ->orWhere('focal_length', 'like', "%{$search}%");
            });
        }

        // Lọc theo Danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Lọc theo Ngàm
        if ($request->filled('mount')) {
            $query->where('mount', 'like', '%' . $request->input('mount') . '%');
        }

        // Lọc theo Thương hiệu
        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->input('brand') . '%');
        }

        // Sắp xếp
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::all();
        $brands = Product::whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Form thêm mới ống kính
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Lưu ống kính mới vào cơ sở dữ liệu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'focal_length' => 'nullable|string|max:100',
            'aperture' => 'nullable|string|max:100',
            'mount' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'image_url' => 'nullable|url',
            'status' => 'required|in:in_stock,out_of_stock',
        ], [
            'name.required' => 'Vui lòng nhập tên ống kính máy ảnh.',
            'category_id.required' => 'Vui lòng chọn danh mục ống kính.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là chữ số hợp lệ.',
            'image_file.image' => 'Tệp tải lên phải là hình ảnh (jpg, png, webp, gif).',
            'image_file.max' => 'Dung lượng ảnh tối đa là 5MB.',
        ]);

        $imagePath = null;

        // Xử lý upload ảnh tệp tin
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/products');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            $file->move($destinationPath, $fileName);
            $imagePath = 'uploads/products/' . $fileName;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        $productData = [
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'sku' => $validated['sku'] ?? null,
            'focal_length' => $validated['focal_length'] ?? null,
            'aperture' => $validated['aperture'] ?? null,
            'mount' => $validated['mount'] ?? null,
            'price' => $validated['price'],
            'stock' => 0, // Tồn kho ban đầu luôn là 0, chờ nhập kho
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'],
        ];

        Product::create($productData);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Thêm mới ống kính "' . $validated['name'] . '" thành công!');
    }

    /**
     * Xem thông tin chi tiết một ống kính
     */
    public function show(Product $product)
    {
        $product->load('category');
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Form chỉnh sửa ống kính
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật thông tin ống kính
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'focal_length' => 'nullable|string|max:100',
            'aperture' => 'nullable|string|max:100',
            'mount' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'gallery_images' => 'nullable|string',
            'sample_images' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'image_url' => 'nullable|url',
            'status' => 'required|in:in_stock,out_of_stock',
        ], [
            'name.required' => 'Vui lòng nhập tên ống kính máy ảnh.',
            'category_id.required' => 'Vui lòng chọn danh mục ống kính.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là chữ số hợp lệ.',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'image_file.image' => 'Tệp tải lên phải là hình ảnh.',
        ]);

        $imagePath = $product->image;

        // Nếu người dùng tải lên ảnh mới
        if ($request->hasFile('image_file')) {
            // Xóa ảnh cũ nếu nằm trong thư mục uploads
            if ($product->image && !str_starts_with($product->image, 'http') && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            $file = $request->file('image_file');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/products');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            $file->move($destinationPath, $fileName);
            $imagePath = 'uploads/products/' . $fileName;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        $productData = [
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'sku' => $validated['sku'] ?? null,
            'focal_length' => $validated['focal_length'] ?? null,
            'aperture' => $validated['aperture'] ?? null,
            'mount' => $validated['mount'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'],
        ];

        // Process Gallery Images
        if ($request->has('gallery_urls')) {
            $gallery = [];
            $galleryUrls = $request->input('gallery_urls', []);
            $galleryCaps = $request->input('gallery_caps', []);
            $galleryFiles = $request->file('gallery_files', []);

            foreach ($galleryUrls as $i => $url) {
                $cap = $galleryCaps[$i] ?? '';
                $finalUrl = $url;
                
                if (isset($galleryFiles[$i])) {
                    $file = $galleryFiles[$i];
                    $fileName = time() . '_g_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products'), $fileName);
                    $finalUrl = 'uploads/products/' . $fileName;
                }
                
                if (!empty($finalUrl)) {
                    $gallery[] = ['url' => $finalUrl, 'cap' => $cap];
                }
            }
            $productData['gallery_images'] = $gallery;
        }

        // Process Sample Images
        if ($request->has('sample_urls')) {
            $samples = [];
            $sampleUrls = $request->input('sample_urls', []);
            $sampleTags = $request->input('sample_tags', []);
            $sampleTexts = $request->input('sample_texts', []);
            $sampleFiles = $request->file('sample_files', []);

            foreach ($sampleUrls as $i => $url) {
                $tag = $sampleTags[$i] ?? '';
                $text = $sampleTexts[$i] ?? '';
                $finalUrl = $url;
                
                if (isset($sampleFiles[$i])) {
                    $file = $sampleFiles[$i];
                    $fileName = time() . '_s_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products'), $fileName);
                    $finalUrl = 'uploads/products/' . $fileName;
                }
                
                if (!empty($finalUrl)) {
                    $samples[] = ['url' => $finalUrl, 'tag' => $tag, 'text' => $text];
                }
            }
            $productData['sample_images'] = $samples;
        }

        $product->update($productData);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Cập nhật ống kính "' . $product->name . '" thành công!');
    }

    /**
     * Xóa ống kính khỏi cơ sở dữ liệu
     */
    public function destroy(Product $product)
    {
        $name = $product->name;

        // Xóa ảnh local nếu có
        if ($product->image && !str_starts_with($product->image, 'http') && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', "Đã xóa ống kính '{$name}' thành công!");
    }

    /**
     * Cập nhật hàng loạt theo thương hiệu
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string',
            'price_percent' => 'nullable|numeric',
            'fixed_price' => 'nullable|numeric',
        ]);

        $products = Product::where('brand', $validated['brand'])->get();

        foreach ($products as $product) {
            if ($request->filled('fixed_price')) {
                $product->price = $validated['fixed_price'];
            } elseif ($request->filled('price_percent')) {
                $product->price = $product->price * (1 + ($validated['price_percent'] / 100));
            }
            $product->save();
        }

        return redirect()->route('admin.products.index')
                         ->with('success', 'Đã cập nhật hàng loạt sản phẩm thương hiệu "' . $validated['brand'] . '".');
    }
}
