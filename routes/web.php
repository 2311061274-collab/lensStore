<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GHNController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\ForgotPasswordController;

// Forgot Password Routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetOtp'])->name('password.email');
Route::get('forgot-password/verify', [ForgotPasswordController::class, 'showVerifyOtpForm'])->name('password.verify.form');
Route::post('forgot-password/verify', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verify');
Route::get('reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// OTP Email Verification Routes
Route::middleware('web')->group(function () {
    // Hiển thị trang nhập OTP (không cần auth - vì user chưa được tạo lúc đăng ký mới)
    Route::get('/email/verify', [AuthController::class, 'showVerifyOtpForm'])->name('verification.notice');
    // Xử lý mã OTP người dùng nhập
    Route::post('/email/verify/otp', [AuthController::class, 'verifyOtp'])->name('verification.verify-otp');
    // Gửi lại OTP
    Route::post('/email/resend-otp', [AuthController::class, 'resendOtp'])->name('verification.send');
});

use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoodsIssueController;
use App\Http\Controllers\Admin\GoodsReceiptController;
use App\Http\Controllers\Admin\QcInspectionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReturnRequestController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Api\VoucherApiController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MomoController;
use App\Http\Controllers\OrderActionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;

// Cart Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buy-now');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/update-selection', [CartController::class, 'updateSelection'])->name('cart.update-selection');
    Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout dùng GHN
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('checkout.process');

    // Đơn hàng của tôi
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Các tính năng sau khi mua hàng
    Route::post('/orders/{order}/confirm-received', [OrderActionController::class, 'confirmReceived'])->name('orders.confirm-received');
    Route::get('/orders/{order}/return', [OrderActionController::class, 'returnRequestForm'])->name('orders.return');
    Route::post('/orders/{order}/return', [OrderActionController::class, 'returnRequestStore'])->name('orders.return.store');
    Route::get('/orders/{order}/review', [OrderActionController::class, 'reviewForm'])->name('orders.review');
    Route::post('/orders/{order}/review', [OrderActionController::class, 'reviewStore'])->name('orders.review.store');

    // GHN API (AJAX)
    Route::get('/ghn/provinces', [GHNController::class, 'provinces'])->name('ghn.provinces');
    Route::get('/ghn/districts', [GHNController::class, 'districts'])->name('ghn.districts');
    Route::get('/ghn/wards', [GHNController::class, 'wards'])->name('ghn.wards');
    Route::get('/ghn/wards-by-province', [GHNController::class, 'wardsByProvince'])->name('ghn.wards-by-province');
    Route::post('/ghn/calculate-fee', [GHNController::class, 'calculateFee'])->name('ghn.calculate-fee');

    // Quản lý tài khoản cá nhân
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/addresses', [UserAddressController::class, 'store'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [UserAddressController::class, 'update'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [UserAddressController::class, 'destroy'])->name('profile.addresses.destroy');

    // Áp dụng Voucher (Storefront)
    Route::post('/api/voucher/apply', [VoucherApiController::class, 'apply'])->name('api.voucher.apply');

    // MoMo - Thanh toán lại & callback
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('momo.pay-again');
    Route::get('/user/payment/momo/callback', [MomoController::class, 'callback'])->name('momo.callback');

});

// Hỗ trợ khách hàng - Chat trực tiếp & Trả lời tự động (Cả khách vãng lai & thành viên)
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/unread', [UserChatController::class, 'unread'])->name('chat.unread');
});

// MoMo IPN (Webhook từ server MoMo - không cần auth, không cần CSRF)
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('momo.ipn');

// Storefront (Khách hàng)
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');

// Danh sách sản phẩm (Song ngữ: /san-pham & /products)
Route::get('/san-pham', [StorefrontController::class, 'productsPage'])->name('storefront.products');
Route::get('/products', [StorefrontController::class, 'productsPage']);

// Chi tiết sản phẩm (Song ngữ: /product/{id} & /san-pham/{id})
Route::get('/product/{id}', [StorefrontController::class, 'show'])->name('storefront.show');
Route::get('/san-pham/{id}', [StorefrontController::class, 'show']);

// Tin tức (Song ngữ: /tin-tuc & /news)
Route::get('/tin-tuc', [StorefrontController::class, 'news'])->name('storefront.news');
Route::get('/news', [StorefrontController::class, 'news']);
Route::get('/tin-tuc-ajax', [StorefrontController::class, 'newsAjax'])->name('storefront.news.ajax');
Route::get('/tin-tuc/{id}', [StorefrontController::class, 'showNews'])->name('storefront.news.show');
Route::get('/news/{id}', [StorefrontController::class, 'showNews']);

// Giới thiệu (Song ngữ: /gioi-thieu & /about)
Route::get('/gioi-thieu', [StorefrontController::class, 'about'])->name('storefront.about');
Route::get('/about', [StorefrontController::class, 'about']);

// Hỗ trợ khách hàng (Song ngữ: /ho-tro, /support & /contact)
Route::get('/ho-tro', [StorefrontController::class, 'support'])->name('storefront.support');
Route::get('/support', [StorefrontController::class, 'support']);
Route::get('/contact', [StorefrontController::class, 'support']);

// Redirect legacy admin URLs
Route::redirect('/categories', '/admin/categories');
Route::redirect('/users', '/admin/users');
Route::redirect('/vouchers', '/admin/vouchers');

// Admin Panel
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::middleware(['permission:view_dashboard'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'apiData'])->name('dashboard.data');
    });

    Route::middleware(['permission:manage_orders'])->group(function () {
        Route::get('orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    Route::middleware(['permission:manage_categories'])->resource('categories', CategoryController::class);

    Route::middleware(['permission:manage_products'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('products/bulk', [ProductController::class, 'bulkUpdate'])->name('products.bulk');

        Route::resource('goods_receipts', GoodsReceiptController::class)->except(['edit', 'update', 'destroy']);
        Route::post('goods_receipts/{goods_receipt}/complete', [GoodsReceiptController::class, 'complete'])->name('goods_receipts.complete');

        Route::resource('goods_issues', GoodsIssueController::class)->only(['index', 'create', 'store']);

        Route::get('qc_inspections', [QcInspectionController::class, 'index'])->name('qc_inspections.index');
        Route::get('qc_inspections/create', [QcInspectionController::class, 'create'])->name('qc_inspections.create');
        Route::post('qc_inspections', [QcInspectionController::class, 'store'])->name('qc_inspections.store');
    });

    Route::middleware(['permission:manage_customers'])->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::post('customers/{customer}/notes', [CustomerController::class, 'storeNote'])->name('customers.notes.store');
        Route::patch('customers/{customer}/toggle-lock', [CustomerController::class, 'toggleLock'])->name('customers.toggleLock');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });

    Route::middleware(['permission:manage_orders'])->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/toggle', [ReviewController::class, 'toggleVisibility'])->name('reviews.toggle');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('returns', [ReturnRequestController::class, 'index'])->name('returns.index');
        Route::patch('returns/{returnRequest}/status', [ReturnRequestController::class, 'updateStatus'])->name('returns.update-status');
        Route::patch('returns/{returnRequest}/tracking', [ReturnRequestController::class, 'updateTracking'])->name('returns.update-tracking');
        Route::patch('returns/{returnRequest}/confirm-refund', [ReturnRequestController::class, 'confirmRefund'])->name('returns.confirm-refund');
    });

    Route::middleware(['permission:view_reports'])->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export-orders', [ReportController::class, 'exportOrders'])->name('reports.export-orders');
        Route::get('reports/export-revenue', [ReportController::class, 'exportRevenue'])->name('reports.export-revenue');
    });

    Route::middleware(['permission:manage_roles'])->resource('roles', RoleController::class);

    Route::middleware(['permission:manage_users'])->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('users/{user}/avatar', [UserController::class, 'removeAvatar'])->name('users.remove-avatar');
    });

    Route::middleware(['permission:manage_vouchers'])->resource('vouchers', VoucherController::class);

    Route::middleware(['permission:manage_news'])->resource('news', NewsController::class);

    Route::get('chat', [AdminChatController::class, 'index'])->name('chat.index');
    Route::get('chat/users', [AdminChatController::class, 'getUsers'])->name('chat.users');
    Route::get('chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('chat/send', [AdminChatController::class, 'send'])->name('chat.send');
});

// Hỗ trợ truy cập URL có tiền tố /lensStore (tương thích GitHub Pages base path & Apache Alias)
Route::any('/lensStore/{path?}', function (Request $request, $path = null) {
    $target = '/'.ltrim($path ?? '', '/');
    $query = $request->getQueryString();

    return redirect($target.($query ? '?'.$query : ''));
})->where('path', '.*');
