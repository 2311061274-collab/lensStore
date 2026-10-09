# 📜 Coding Standards & Conventions

> **Quy chuẩn lập trình, mẫu thiết kế và phong cách viết code bắt buộc áp dụng cho toàn bộ dự án LensStore.**

---

## 1. Quy Chuẩn PHP & Laravel Backend

### 1.1. Chuẩn Cú Pháp & Phiên Bản
- Dự án chạy trên **PHP 8.3 / 8.4** và **Laravel 13**.
- Tuân thủ nghiêm ngặt tiêu chuẩn **PSR-12** và **PSR-4**.
- Sử dụng strict typing và type-hinting rõ ràng trên mọi hàm, phương thức và tham số:
  ```php
  public function calculateDiscount(float $orderValue): float
  public function isStaff(?User $user): bool
  ```

### 1.2. Chuẩn Khai Báo Eloquent Model (Laravel 13 Modern Style)
1. **Sử dụng PHP 8 Attributes** cho `$fillable` và `$hidden` (hoặc mảng `$fillable` truyền thống nhưng khuyến khích attribute):
   ```php
   use Illuminate\Database\Eloquent\Attributes\Fillable;
   use Illuminate\Database\Eloquent\Attributes\Hidden;

   #[Fillable(['name', 'email', 'password', 'role', 'phone', 'avatar'])]
   #[Hidden(['password', 'remember_token'])]
   class User extends Authenticatable
   {
       ...
   }
   ```
2. **Khai báo Casts qua phương thức `casts()`**:
   Không sử dụng thuộc tính protected `$casts = [...]` kiểu cũ. Luôn sử dụng phương thức:
   ```php
   protected function casts(): array
   {
       return [
           'email_verified_at' => 'datetime',
           'price'             => 'decimal:2',
           'stock'             => 'integer',
           'gallery_images'    => 'array',
           'is_active'         => 'boolean',
       ];
   }
   ```
3. **Khai báo Quan Hệ (Relationships)** bắt buộc có Return Type cụ thể:
   ```php
   use Illuminate\Database\Eloquent\Relations\BelongsTo;
   use Illuminate\Database\Eloquent\Relations\HasMany;
   use Illuminate\Database\Eloquent\Relations\MorphTo;

   public function category(): BelongsTo
   {
       return $this->belongsTo(Category::class);
   }

   public function items(): HasMany
   {
       return $this->hasMany(OrderItem::class);
   }

   public function reference(): MorphTo
   {
       return $this->morphTo();
   }
   ```
4. **Accessors & Mutators**:
   - Sử dụng định dạng tiền tệ VNĐ chuẩn qua accessor:
     ```php
     public function getFormattedPriceAttribute(): string
     {
         return number_format($this->price, 0, ',', '.') . ' ₫';
     }
     ```
   - Fallback ảnh đại diện an toàn:
     ```php
     public function getImageUrlAttribute(): string
     {
         if (!empty($this->image)) {
             if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                 return $this->image;
             }
             return asset($this->image);
         }
         return asset('images/default-lens.jpg');
     }
     ```

### 1.3. Controller & Validation
1. **Thin Controller**:
   - Controller chỉ xử lý HTTP request, kiểm tra validation, gọi Service/Model và chuyển hướng/trả view.
   - Không viết logic tích hợp API bên thứ ba (GHN, MoMo) trực tiếp trong Controller; đưa vào `app/Services/`.
2. **Validation có thông báo tiếng Việt**:
   - Mọi request nhập liệu từ client phải được validate đầy đủ với custom error message rõ ràng, dễ hiểu:
   ```php
   $validated = $request->validate([
       'name'        => 'required|string|max:255',
       'category_id' => 'required|exists:categories,id',
       'price'       => 'required|numeric|min:0',
   ], [
       'name.required'        => 'Vui lòng nhập tên ống kính máy ảnh.',
       'category_id.required' => 'Vui lòng chọn danh mục ống kính.',
       'category_id.exists'   => 'Danh mục đã chọn không tồn tại.',
       'price.required'       => 'Vui lòng nhập giá bán.',
       'price.numeric'        => 'Giá bán phải là số hợp lệ.',
   ]);
   ```

### 1.4. Quy Tắc Giao Dịch CSDL (Database Transactions)
- **BẮT BUỘC** bọc trong khối `DB::beginTransaction()` và `DB::rollBack()` khi thực hiện các thao tác ghi dữ liệu đa bước:
  - Tạo đơn hàng & trừ tồn kho (`processCheckout`).
  - Tạo phiếu nhập kho và chi tiết (`GoodsReceiptController@store`).
  - Kiểm định chất lượng hàng hoàn về QC (`QcInspectionController@store`).
  - Hủy đơn hàng và hoàn trả tồn kho (`cancel`).
- Mẫu chuẩn:
  ```php
  DB::beginTransaction();
  try {
      // 1. Tạo bản ghi chính
      $order = Order::create([...]);
      
      // 2. Tạo chi tiết & cập nhật tồn kho
      ...
      
      DB::commit();
      return redirect()->route(...)->with('success', 'Thao tác thành công!');
  } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Lỗi thực thi: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
      return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
  }
  ```

---

## 2. Quy Chuẩn Blade View & Frontend

### 2.1. Cấu Trúc Layout Kế Thừa
- Không viết trang HTML độc lập. Luôn kế thừa từ một trong 3 master layout:
  - `@extends('layouts.app')`: Các trang Storefront phía khách hàng.
  - `@extends('layouts.admin')`: Các trang quản trị quản lý trong `/admin`.
  - `@extends('layouts.auth')`: Các trang xác thực `/login`, `/register`, `/forgot-password`.
- Khai báo tiêu đề rõ ràng: `@section('title', 'Tên Trang — LensStore')`.

### 2.2. Biểu Mẫu (Forms) & Bảo Mật CSRF
- Mọi thẻ `<form method="POST">` bắt buộc phải có chỉ thị `@csrf`.
- Với các phương thức `PUT`, `PATCH`, `DELETE`, bắt buộc dùng method spoofing:
  ```html
  <form action="{{ route('admin.products.update', $product) }}" method="POST">
      @csrf
      @method('PUT')
      ...
  </form>
  ```

### 2.3. Hệ Thống CSS Variables & Tokens Đồng Bộ
Không tạo inline style màu sắc tùy tiện (`style="color: red;"`). Luôn tận dụng hệ thống biến CSS đã được thiết lập sẵn trong Layout:
- `--primary`: Màu chủ đạo (`#4f46e5` trong Admin/App, `#5b21b6` trong Auth).
- `--primary-hover`: Màu hover chủ đạo.
- `--accent`: Màu điểm nhấn (`#f59e0b` vàng cam).
- `--surface`: Màu nền thẻ/card (`#ffffff`).
- `--canvas` / `--background`: Màu nền trang (`#f8fafc`).
- `--line` / `--border`: Đường viền (`#e2e8f0`).
- `--danger`: Màu cảnh báo lỗi (`#ef4444`).
- `--ok` / `--success`: Màu thành công (`#10b981`).
- `--radius` / `--radius-md`: Bo góc chuẩn (`12px` - `14px`).

### 2.4. Hiển Thị Thông Báo (Flash Messages)
Mọi trang Blade đều có vùng hiển thị session flash messages đồng bộ:
```blade
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif
```

---

## 3. Quy Chuẩn JavaScript & AJAX Calls

1. **Lấy CSRF Token**:
   Khi gửi request `POST`, `PUT`, `DELETE` bằng Fetch API, luôn đính kèm header `X-CSRF-TOKEN`:
   ```javascript
   const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

   fetch('/api/route', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': csrfToken,
           'Accept': 'application/json'
       },
       body: JSON.stringify(data)
   });
   ```
2. **Xử lý phản hồi JSON an toàn**:
   Luôn bắt lỗi `try...catch` và kiểm tra response status trước khi cập nhật DOM. Tránh để trang web bị đơ khi mạng gặp sự cố.

---

## 4. Quy Chuẩn Kiểm Quyền & Bảo Mật (Authorization & Security)

1. **Quyền sở hữu tài nguyên (Resource Ownership)**:
   Trước khi cho phép xem hoặc sửa đổi dữ liệu người dùng (đơn hàng, địa chỉ, giỏ hàng), luôn kiểm tra quyền sở hữu:
   ```php
   if ($order->user_id !== Auth::id()) {
       abort(403, 'Bạn không có quyền truy cập vào đơn hàng này.');
   }
   ```
2. **Bảo vệ Phân quyền Admin**:
   Các route quản trị trong `routes/web.php` bắt buộc được bọc trong middleware kiểm tra permission tương ứng:
   ```php
   Route::middleware(['permission:manage_orders'])->group(function () {
       Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
   });
   ```
3. **Bảo mật Upload File**:
   - Xác thực đuôi mở rộng và dung lượng tệp tin: `image|mimes:jpeg,png,jpg,webp,gif|max:5120`.
   - Sinh tên tệp tin duy nhất bằng timestamp và `uniqid()`: `time() . '_' . uniqid() . '.' . $extension`.
   - Lưu trữ trong thư mục có kiểm soát: `public/uploads/products/`.
