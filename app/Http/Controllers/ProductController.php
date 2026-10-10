<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
            $query->where('mount', 'like', '%'.$request->input('mount').'%');
        }

        // Lọc theo Thương hiệu
        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%'.$request->input('brand').'%');
        }

        // Lọc theo tình trạng tồn kho (Tích hợp liên kết từ Dashboard)
        if ($request->filled('stock_status')) {
            $stockStatus = $request->input('stock_status');
            if ($stockStatus === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($stockStatus === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<', 3);
            } elseif ($stockStatus === 'alert') {
                $query->where('stock', '<', 3);
            } elseif ($stockStatus === 'in_stock') {
                $query->where('stock', '>=', 3);
            }
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
            'sku' => 'nullable|string|max:100|unique:products,sku',
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
            'sku.unique' => 'Mã SKU này đã tồn tại trên hệ thống.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là chữ số hợp lệ.',
            'image_file.image' => 'Tệp tải lên phải là hình ảnh (jpg, png, webp, gif).',
            'image_file.max' => 'Dung lượng ảnh tối đa là 5MB.',
        ]);

        $imagePath = null;
        $destinationPath = public_path('uploads/products');

        // Xử lý upload ảnh chính
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($file->isValid()) {
                File::ensureDirectoryExists($destinationPath);
                $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $ext = 'jpg';
                }
                $fileName = 'prod_'.date('Ymd_His').'_'.Str::random(12).'.'.$ext;
                $file->move($destinationPath, $fileName);
                $imagePath = 'uploads/products/'.$fileName;
            }
        } elseif ($request->filled('image_url')) {
            $rawUrl = trim($request->input('image_url'));
            $parsed = parse_url($rawUrl);
            if (! isset($parsed['scheme']) || ! in_array(strtolower($parsed['scheme']), ['http', 'https'])) {
                return back()->withErrors(['image_url' => 'URL hình ảnh phải có giao thức http:// hoặc https:// hợp lệ.'])->withInput();
            }
            $imagePath = $rawUrl;
        }

        $productData = [
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'sku' => $validated['sku'] ?? null,
            'focal_length' => $validated['focal_length'] ?? null,
            'aperture' => $validated['aperture'] ?? null,
            'mount' => $validated['mount'] ?? null,
            'price' => $validated['price'],
            'stock' => 0, // Tồn kho ban đầu luôn là 0, chỉ tăng thông qua Phiếu Nhập Kho (Goods Receipt)
            'description' => $validated['description'] ?? null,
            'image' => $imagePath,
            'status' => $validated['status'],
        ];

        // Xử lý Gallery Images nếu có
        if ($request->has('gallery_urls')) {
            $gallery = [];
            $galleryUrls = $request->input('gallery_urls', []);
            $galleryCaps = $request->input('gallery_caps', []);
            $galleryFiles = $request->file('gallery_files', []);

            File::ensureDirectoryExists($destinationPath);

            foreach ($galleryUrls as $i => $url) {
                $cap = $galleryCaps[$i] ?? '';
                $finalUrl = $url;

                if (isset($galleryFiles[$i]) && $galleryFiles[$i]->isValid()) {
                    $gFile = $galleryFiles[$i];
                    $gExt = strtolower($gFile->guessExtension() ?: $gFile->getClientOriginalExtension());
                    $gName = 'prod_g_'.date('Ymd_His').'_'.Str::random(12).'.'.$gExt;
                    $gFile->move($destinationPath, $gName);
                    $finalUrl = 'uploads/products/'.$gName;
                }

                if (! empty($finalUrl)) {
                    $gallery[] = ['url' => $finalUrl, 'cap' => $cap];
                }
            }
            $productData['gallery_images'] = $gallery;
        }

        // Xử lý Sample Images nếu có
        if ($request->has('sample_urls')) {
            $samples = [];
            $sampleUrls = $request->input('sample_urls', []);
            $sampleTags = $request->input('sample_tags', []);
            $sampleTexts = $request->input('sample_texts', []);
            $sampleFiles = $request->file('sample_files', []);

            File::ensureDirectoryExists($destinationPath);

            foreach ($sampleUrls as $i => $url) {
                $tag = $sampleTags[$i] ?? '';
                $text = $sampleTexts[$i] ?? '';
                $finalUrl = $url;

                if (isset($sampleFiles[$i]) && $sampleFiles[$i]->isValid()) {
                    $sFile = $sampleFiles[$i];
                    $sExt = strtolower($sFile->guessExtension() ?: $sFile->getClientOriginalExtension());
                    $sName = 'prod_s_'.date('Ymd_His').'_'.Str::random(12).'.'.$sExt;
                    $sFile->move($destinationPath, $sName);
                    $finalUrl = 'uploads/products/'.$sName;
                }

                if (! empty($finalUrl)) {
                    $samples[] = ['url' => $finalUrl, 'tag' => $tag, 'text' => $text];
                }
            }
            $productData['sample_images'] = $samples;
        }

        Product::create($productData);

        return redirect()->route('admin.products.index')
            ->with('success', 'Thêm mới ống kính "'.$validated['name'].'" thành công! Tồn kho hiện tại là 0, vui lòng tạo Phiếu Nhập Kho để nạp số lượng.');
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
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$product->id,
            'focal_length' => 'nullable|string|max:100',
            'aperture' => 'nullable|string|max:100',
            'mount' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
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
            'sku.unique' => 'Mã SKU này đã tồn tại trên sản phẩm khác.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là chữ số hợp lệ.',
            'image_file.image' => 'Tệp tải lên phải là hình ảnh (jpg, png, webp, gif).',
            'image_file.max' => 'Dung lượng ảnh tối đa là 5MB.',
        ]);

        $oldImage = $product->image;
        $imagePath = $oldImage; // Mặc định: Giữ nguyên ảnh cũ nếu không chọn ảnh mới
        $destinationPath = public_path('uploads/products');
        $hasNewImage = false;

        // Nếu người dùng tải lên ảnh mới từ máy tính
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            if ($file->isValid()) {
                File::ensureDirectoryExists($destinationPath);
                $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $ext = 'jpg';
                }
                $fileName = 'prod_'.date('Ymd_His').'_'.Str::random(12).'.'.$ext;
                $file->move($destinationPath, $fileName);

                // Lưu ảnh mới thành công -> chuẩn bị gán và đánh dấu thay đổi
                $imagePath = 'uploads/products/'.$fileName;
                $hasNewImage = true;
            }
        } elseif ($request->filled('image_url')) {
            $rawUrl = trim($request->input('image_url'));
            $parsed = parse_url($rawUrl);
            if (! isset($parsed['scheme']) || ! in_array(strtolower($parsed['scheme']), ['http', 'https'])) {
                return back()->withErrors(['image_url' => 'URL hình ảnh phải có giao thức http:// hoặc https:// hợp lệ.'])->withInput();
            }
            if ($rawUrl !== $oldImage) {
                $imagePath = $rawUrl;
                $hasNewImage = true;
            }
        }

        // Không cho phép sửa tồn kho trực tiếp từ form edit (Bảo đảm nguyên tắc WMS/Mini-ERP)
        $productData = [
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'sku' => $validated['sku'] ?? null,
            'focal_length' => $validated['focal_length'] ?? null,
            'aperture' => $validated['aperture'] ?? null,
            'mount' => $validated['mount'] ?? null,
            'price' => $validated['price'],
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

            File::ensureDirectoryExists($destinationPath);

            foreach ($galleryUrls as $i => $url) {
                $cap = $galleryCaps[$i] ?? '';
                $finalUrl = $url;

                if (isset($galleryFiles[$i]) && $galleryFiles[$i]->isValid()) {
                    $gFile = $galleryFiles[$i];
                    $gExt = strtolower($gFile->guessExtension() ?: $gFile->getClientOriginalExtension());
                    $gName = 'prod_g_'.date('Ymd_His').'_'.Str::random(12).'.'.$gExt;
                    $gFile->move($destinationPath, $gName);
                    $finalUrl = 'uploads/products/'.$gName;
                }

                if (! empty($finalUrl)) {
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

            File::ensureDirectoryExists($destinationPath);

            foreach ($sampleUrls as $i => $url) {
                $tag = $sampleTags[$i] ?? '';
                $text = $sampleTexts[$i] ?? '';
                $finalUrl = $url;

                if (isset($sampleFiles[$i]) && $sampleFiles[$i]->isValid()) {
                    $sFile = $sampleFiles[$i];
                    $sExt = strtolower($sFile->guessExtension() ?: $sFile->getClientOriginalExtension());
                    $sName = 'prod_s_'.date('Ymd_His').'_'.Str::random(12).'.'.$sExt;
                    $sFile->move($destinationPath, $sName);
                    $finalUrl = 'uploads/products/'.$sName;
                }

                if (! empty($finalUrl)) {
                    $samples[] = ['url' => $finalUrl, 'tag' => $tag, 'text' => $text];
                }
            }
            $productData['sample_images'] = $samples;
        }

        // Cập nhật cơ sở dữ liệu
        $product->update($productData);

        // Sau khi lưu DB thành công: Nếu có ảnh mới và ảnh cũ là file local, dọn dẹp file cũ nếu không còn ai dùng
        if ($hasNewImage && $oldImage && ! str_starts_with($oldImage, 'http')) {
            $stillUsed = Product::where('id', '!=', $product->id)->where('image', $oldImage)->exists();
            if (! $stillUsed && File::exists(public_path($oldImage))) {
                File::delete(public_path($oldImage));
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật ống kính "'.$product->name.'" thành công!');
    }

    /**
     * Xóa ống kính khỏi cơ sở dữ liệu (Chặn xóa cứng nếu đã có giao dịch)
     */
    public function destroy(Product $product)
    {
        $name = $product->name;

        // Kiểm tra xem sản phẩm đã phát sinh giao dịch đơn hàng hoặc biến động thẻ kho chưa
        $hasOrders = $product->orderItems()->exists();
        $hasTransactions = InventoryTransaction::where('product_id', $product->id)->exists();

        if ($hasOrders || $hasTransactions) {
            return back()->with('error', "Không thể xóa ống kính '{$name}' vì đã phát sinh lịch sử đơn hàng hoặc thẻ kho. Hãy chuyển trạng thái sản phẩm sang 'Hết hàng' (Ngừng kinh doanh) để bảo vệ toàn vẹn dữ liệu kế toán.");
        }

        // Xóa ảnh local nếu không còn sản phẩm khác sử dụng
        if ($product->image && ! str_starts_with($product->image, 'http')) {
            $stillUsed = Product::where('id', '!=', $product->id)->where('image', $product->image)->exists();
            if (! $stillUsed && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
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
            ->with('success', 'Đã cập nhật hàng loạt sản phẩm thương hiệu "'.$validated['brand'].'".');
    }
}
