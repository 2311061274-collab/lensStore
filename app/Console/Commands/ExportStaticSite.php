<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Models\Category;
use App\Models\GoodsReceipt;
use App\Models\News;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;

class ExportStaticSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-static {--base-path= : Base URL path for GitHub Pages subfolder (e.g. /lensStore)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Render and export LensStore storefront as a 100% compatible static site for GitHub Pages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rawBasePath = $this->option('base-path') ?? '';
        $basePrefix = ! empty($rawBasePath) ? '/'.trim($rawBasePath, '/') : '';
        $distDir = base_path('dist');

        $this->info("🚀 Bắt đầu xuất tĩnh website cho GitHub Pages (Base prefix: '{$basePrefix}')...");

        // 1. Dọn dẹp và tạo lại thư mục dist
        if (File::exists($distDir)) {
            File::deleteDirectory($distDir);
        }
        File::makeDirectory($distDir, 0755, true);

        // 2. Sao chép tài nguyên tĩnh (Vite build assets, uploads, images, favicon)
        $this->info('📦 Đang sao chép assets...');

        if (File::exists(public_path('build'))) {
            File::copyDirectory(public_path('build'), $distDir.'/build');
            $this->line('   - Đã sao chép public/build');
        } else {
            $this->warn("   ! Cảnh báo: public/build chưa tồn tại. Hãy chạy 'npm run build' trước.");
        }

        if (File::exists(public_path('uploads'))) {
            File::copyDirectory(public_path('uploads'), $distDir.'/uploads');
            $this->line('   - Đã sao chép public/uploads');
        }

        if (File::exists(public_path('storage'))) {
            File::copyDirectory(public_path('storage'), $distDir.'/storage');
            $this->line('   - Đã sao chép public/storage');
        }

        if (File::exists(public_path('images'))) {
            File::copyDirectory(public_path('images'), $distDir.'/images');
            $this->line('   - Đã sao chép public/images');
        }

        if (File::exists(public_path('favicon.ico'))) {
            File::copy(public_path('favicon.ico'), $distDir.'/favicon.ico');
        }

        // Tạo file .nojekyll để GitHub Pages không dùng Jekyll xử lý các file _
        File::put($distDir.'/.nojekyll', '');
        $this->line('   - Đã tạo .nojekyll');

        // 3. Danh sách các trang tĩnh cần render (Key: Route path, Value: array config)
        $allPages = [];

        // Trang công khai Storefront
        $publicPages = [
            '/' => '',
            '/san-pham' => 'san-pham',
            '/products' => 'products',
            '/gioi-thieu' => 'gioi-thieu',
            '/about' => 'about',
            '/ho-tro' => 'ho-tro',
            '/support' => 'support',
            '/contact' => 'contact',
            '/tin-tuc' => 'tin-tuc',
            '/news' => 'news',
            '/login' => 'login',
            '/register' => 'register',
            '/forgot-password' => 'forgot-password',
        ];

        foreach ($publicPages as $routePath => $slug) {
            $allPages[$routePath] = ['slug' => $slug, 'user' => null, 'session' => []];
        }

        // Trang xác thực đặc thù có session giả lập
        $allPages['/email/verify'] = [
            'slug' => 'email/verify',
            'user' => null,
            'session' => [
                'pending_registration' => [
                    'name' => 'Demo User',
                    'email' => 'demo@example.com',
                    'phone' => '0901234567',
                ],
                'otp_code' => '123456',
                'otp_email' => 'demo@example.com',
                'otp_expires_at' => now()->addMinutes(15),
            ],
        ];

        $allPages['/forgot-password/verify'] = [
            'slug' => 'forgot-password/verify',
            'user' => null,
            'session' => [
                'reset_password_email' => 'demo@example.com',
                'otp_code' => '123456',
                'otp_expires_at' => now()->addMinutes(15),
            ],
        ];

        $allPages['/reset-password'] = [
            'slug' => 'reset-password',
            'user' => null,
            'session' => [
                'reset_password_email' => 'demo@example.com',
                'reset_password_verified' => true,
            ],
        ];

        // Chuẩn bị tài khoản Admin và Khách hàng mẫu
        $adminUser = null;
        $customerUser = null;
        try {
            $adminUser = User::where('role', 'admin')->first() ?: User::first();
            $customerUser = User::where('role', 'customer')->first() ?: $adminUser;

            if ($customerUser) {
                $sampleProduct = Product::first();
                if ($sampleProduct) {
                    Cart::updateOrCreate(
                        ['user_id' => $customerUser->id, 'product_id' => $sampleProduct->id],
                        ['quantity' => 1, 'is_selected' => true]
                    );
                }
            }
        } catch (\Throwable $e) {
            $this->warn('   ! Không thể nạp Demo Users: '.$e->getMessage());
        }

        // Thu thập toàn bộ sản phẩm ống kính từ database
        try {
            $products = Product::all();
            foreach ($products as $prod) {
                $allPages['/product/'.$prod->id] = ['slug' => 'product/'.$prod->id, 'user' => null, 'session' => []];
                $allPages['/san-pham/'.$prod->id] = ['slug' => 'san-pham/'.$prod->id, 'user' => null, 'session' => []];
            }
            $this->info('   - Đã thu thập '.$products->count().' sản phẩm ống kính.');
        } catch (\Throwable $e) {
            $this->warn('   ! Không thể đọc danh sách Product từ database: '.$e->getMessage());
        }

        // Thu thập toàn bộ bài viết tin tức từ database
        try {
            $newsArticles = News::all();
            foreach ($newsArticles as $article) {
                $allPages['/tin-tuc/'.$article->id] = ['slug' => 'tin-tuc/'.$article->id, 'user' => null, 'session' => []];
                $allPages['/news/'.$article->id] = ['slug' => 'news/'.$article->id, 'user' => null, 'session' => []];
            }
            $this->info('   - Đã thu thập '.$newsArticles->count().' bài viết tin tức.');
        } catch (\Throwable $e) {
            $this->warn('   ! Không thể đọc danh sách News từ database: '.$e->getMessage());
        }

        // Trang trải nghiệm giỏ hàng & tài khoản khách hàng
        $customerPages = [
            '/cart' => 'cart',
            '/checkout' => 'checkout',
            '/wishlist' => 'wishlist',
            '/orders' => 'orders',
            '/profile' => 'profile',
        ];

        foreach ($customerPages as $routePath => $slug) {
            $allPages[$routePath] = ['slug' => $slug, 'user' => $customerUser, 'session' => []];
        }

        // Đơn hàng của khách hàng & các form hành động (chi tiết, đổi trả, đánh giá)
        try {
            $orders = Order::all();
            foreach ($orders as $ord) {
                $allPages['/orders/'.$ord->id] = [
                    'slug' => 'orders/'.$ord->id,
                    'user' => $customerUser,
                    'session' => [],
                ];

                // Form đánh giá
                $allPages['/orders/'.$ord->id.'/review'] = [
                    'slug' => 'orders/'.$ord->id.'/review',
                    'user' => $customerUser,
                    'session' => [],
                ];

                // Form đổi trả (cho đơn không có return request trước đó)
                if (! ReturnRequest::where('order_id', $ord->id)->exists() && $ord->status === 'completed') {
                    $allPages['/orders/'.$ord->id.'/return'] = [
                        'slug' => 'orders/'.$ord->id.'/return',
                        'user' => $customerUser,
                        'session' => [],
                    ];
                }
            }
            $this->info('   - Đã thu thập '.$orders->count().' đơn hàng khách hàng.');
        } catch (\Throwable $e) {
            $this->warn('   ! Lỗi thu thập đơn hàng: '.$e->getMessage());
        }

        // Trang quản trị hệ thống Admin
        $adminBasePages = [
            '/admin' => 'admin',
            '/admin/orders' => 'admin/orders',
            '/admin/products' => 'admin/products',
            '/admin/products/create' => 'admin/products/create',
            '/admin/categories' => 'admin/categories',
            '/admin/categories/create' => 'admin/categories/create',
            '/admin/customers' => 'admin/customers',
            '/admin/reviews' => 'admin/reviews',
            '/admin/returns' => 'admin/returns',
            '/admin/reports' => 'admin/reports',
            '/admin/roles' => 'admin/roles',
            '/admin/roles/create' => 'admin/roles/create',
            '/admin/users' => 'admin/users',
            '/admin/users/create' => 'admin/users/create',
            '/admin/vouchers' => 'admin/vouchers',
            '/admin/vouchers/create' => 'admin/vouchers/create',
            '/admin/news' => 'admin/news',
            '/admin/news/create' => 'admin/news/create',
            '/admin/goods_receipts' => 'admin/goods_receipts',
            '/admin/goods_receipts/create' => 'admin/goods_receipts/create',
            '/admin/goods_issues' => 'admin/goods_issues',
            '/admin/goods_issues/create' => 'admin/goods_issues/create',
            '/admin/qc_inspections' => 'admin/qc_inspections',
            '/admin/qc_inspections/create' => 'admin/qc_inspections/create',
            '/admin/chat' => 'admin/chat',
        ];

        foreach ($adminBasePages as $routePath => $slug) {
            $allPages[$routePath] = ['slug' => $slug, 'user' => $adminUser, 'session' => []];
        }

        // Thu thập các trang Show/Edit trong Admin cho từng bản ghi thực tế
        try {
            // Chi tiết đơn hàng trong Admin
            foreach (Order::all() as $ord) {
                $allPages['/admin/orders/'.$ord->id] = ['slug' => 'admin/orders/'.$ord->id, 'user' => $adminUser, 'session' => []];
            }

            // Chỉnh sửa & Chi tiết sản phẩm trong Admin
            foreach (Product::all() as $p) {
                $allPages['/admin/products/'.$p->id.'/edit'] = ['slug' => 'admin/products/'.$p->id.'/edit', 'user' => $adminUser, 'session' => []];
                $allPages['/admin/products/'.$p->id] = ['slug' => 'admin/products/'.$p->id, 'user' => $adminUser, 'session' => []];
            }

            // Chỉnh sửa & Chi tiết danh mục
            foreach (Category::all() as $cat) {
                $allPages['/admin/categories/'.$cat->id.'/edit'] = ['slug' => 'admin/categories/'.$cat->id.'/edit', 'user' => $adminUser, 'session' => []];
                $allPages['/admin/categories/'.$cat->id] = ['slug' => 'admin/categories/'.$cat->id, 'user' => $adminUser, 'session' => []];
            }

            // Chi tiết phiếu nhập kho
            foreach (GoodsReceipt::all() as $gr) {
                $allPages['/admin/goods_receipts/'.$gr->id] = ['slug' => 'admin/goods_receipts/'.$gr->id, 'user' => $adminUser, 'session' => []];
            }

            // Chi tiết khách hàng
            foreach (User::where('role', 'customer')->get() as $cust) {
                $allPages['/admin/customers/'.$cust->id] = ['slug' => 'admin/customers/'.$cust->id, 'user' => $adminUser, 'session' => []];
            }

            // Chỉnh sửa vai trò (Role)
            foreach (Role::all() as $role) {
                $allPages['/admin/roles/'.$role->id.'/edit'] = ['slug' => 'admin/roles/'.$role->id.'/edit', 'user' => $adminUser, 'session' => []];
            }

            // Chỉnh sửa tài khoản người dùng
            foreach (User::all() as $u) {
                $allPages['/admin/users/'.$u->id.'/edit'] = ['slug' => 'admin/users/'.$u->id.'/edit', 'user' => $adminUser, 'session' => []];
                $allPages['/admin/users/'.$u->id] = ['slug' => 'admin/users/'.$u->id, 'user' => $adminUser, 'session' => []];
            }

            // Chỉnh sửa mã giảm giá
            foreach (Voucher::all() as $v) {
                $allPages['/admin/vouchers/'.$v->id.'/edit'] = ['slug' => 'admin/vouchers/'.$v->id.'/edit', 'user' => $adminUser, 'session' => []];
            }

            // Chỉnh sửa bài viết tin tức
            foreach (News::all() as $n) {
                $allPages['/admin/news/'.$n->id.'/edit'] = ['slug' => 'admin/news/'.$n->id.'/edit', 'user' => $adminUser, 'session' => []];
            }
        } catch (\Throwable $e) {
            $this->warn('   ! Lỗi thu thập chi tiết CRUD Admin: '.$e->getMessage());
        }

        // 4. Render từng trang và ghi tệp Dual-Path
        $this->info('📄 Đang render và chuẩn hóa '.count($allPages).' trang cho GitHub Pages...');
        $kernel = app()->make(Kernel::class);

        foreach ($allPages as $routePath => $pageConfig) {
            $slug = $pageConfig['slug'];
            $targetUser = $pageConfig['user'];
            $sessionData = $pageConfig['session'] ?? [];
            try {
                $request = Request::create($routePath, 'GET');
                app()->instance('request', $request);

                if ($targetUser) {
                    auth()->login($targetUser);
                } else {
                    auth()->logout();
                }

                if (! empty($sessionData)) {
                    $session = app('session')->driver();
                    $session->setId('static-export-session');
                    $session->start();
                    foreach ($sessionData as $k => $v) {
                        $session->put($k, $v);
                    }
                    $request->setLaravelSession($session);
                }

                $response = $kernel->handle($request);
                $html = $response->getContent();
                $kernel->terminate($request, $response);

                // Chuẩn hóa toàn bộ URL, triệt tiêu localhost và chuyển sang basePrefix
                $html = $this->normalizeHtmlForGitHubPages($html, $basePrefix);

                // Ghi file theo Dual-Path
                if ($slug === '') {
                    // Trang chủ: index.html
                    File::put($distDir.'/index.html', $html);
                    $this->line('   ✓ Rendered: [Home] -> index.html');
                } else {
                    // 1. Dạng thư mục có index.html: vd dist/gioi-thieu/index.html (cho URL .../gioi-thieu/)
                    $subDir = $distDir.'/'.$slug;
                    if (! File::exists($subDir)) {
                        File::makeDirectory($subDir, 0755, true);
                    }
                    File::put($subDir.'/index.html', $html);

                    // 2. Dạng tệp .html đơn lẻ: vd dist/gioi-thieu.html (cho URL .../gioi-thieu)
                    $singleFile = $distDir.'/'.$slug.'.html';
                    $parentDir = dirname($singleFile);
                    if (! File::exists($parentDir)) {
                        File::makeDirectory($parentDir, 0755, true);
                    }
                    File::put($singleFile, $html);

                    $this->line("   ✓ Rendered: {$routePath} -> {$slug}/index.html & {$slug}.html");
                }
            } catch (\Throwable $e) {
                $this->error("   ✗ Lỗi khi render {$routePath}: ".$e->getMessage());
            }
        }

        // Reset auth state sau khi render
        try {
            auth()->logout();
        } catch (\Throwable $e) {
        }

        // 5. Tạo 404.html chuyên nghiệp cho GitHub Pages
        $homeUrl = ! empty($basePrefix) ? $basePrefix.'/' : '/';
        $notFoundHtml = $this->generate404Page($homeUrl);
        File::put($distDir.'/404.html', $notFoundHtml);
        $this->line('   - Đã tạo 404.html thân thiện cho GitHub Pages');

        $this->info('🎉 Xuất tĩnh hoàn tất 100%! Đã xử lý triệt để link localhost, hỗ trợ Dual-Path và Demo Mode.');

        return Command::SUCCESS;
    }

    /**
     * Chuẩn hóa toàn diện mã HTML để tương thích tuyệt đối với GitHub Pages.
     */
    protected function normalizeHtmlForGitHubPages(string $html, string $basePrefix): string
    {
        $targetBase = ! empty($basePrefix) ? $basePrefix : '';
        $homeUrl = ! empty($targetBase) ? $targetBase.'/' : '/';

        // 1. Xóa bỏ hoàn toàn mọi URL tuyệt đối localhost (kể cả escape slashes trong JS)
        $localhostPatterns = [
            'http://localhost:8000',
            'https://localhost:8000',
            'http://127.0.0.1:8000',
            'https://127.0.0.1:8000',
            'http://localhost',
            'https://localhost',
            'http://127.0.0.1',
            'https://127.0.0.1',
            'http:\/\/localhost:8000',
            'https:\/\/localhost:8000',
            'http:\/\/127.0.0.1:8000',
            'https:\/\/127.0.0.1:8000',
            'http:\/\/localhost',
            'https:\/\/localhost',
            'http:\/\/127.0.0.1',
            'https:\/\/127.0.0.1',
        ];

        $escapedTargetBase = str_replace('/', '\/', $targetBase);

        foreach ($localhostPatterns as $lh) {
            $isEscaped = str_contains($lh, '\/');
            $replacement = $isEscaped ? $escapedTargetBase : $targetBase;
            $slash = $isEscaped ? '\/' : '/';
            $html = str_replace($lh.$slash, $replacement.$slash, $html);
            $html = str_replace($lh, $replacement, $html);
        }

        // 2. Xử lý các liên kết tương đối bắt đầu bằng /
        if (! empty($targetBase)) {
            // Thay thế liên kết assets
            $html = preg_replace('/href="\/(build\/[^"]*)"/', 'href="'.$targetBase.'/$1"', $html);
            $html = preg_replace('/src="\/(build\/[^"]*)"/', 'src="'.$targetBase.'/$1"', $html);
            $html = preg_replace('/src="\/(uploads\/[^"]*)"/', 'src="'.$targetBase.'/$1"', $html);
            $html = preg_replace('/href="\/(uploads\/[^"]*)"/', 'href="'.$targetBase.'/$1"', $html);
            $html = preg_replace('/src="\/(storage\/[^"]*)"/', 'src="'.$targetBase.'/$1"', $html);
            $html = preg_replace('/href="\/(storage\/[^"]*)"/', 'href="'.$targetBase.'/$1"', $html);
            $html = preg_replace('/src="\/(images\/[^"]*)"/', 'src="'.$targetBase.'/$1"', $html);
            $html = preg_replace('/href="\/(images\/[^"]*)"/', 'href="'.$targetBase.'/$1"', $html);
            $html = str_replace('href="/favicon.ico"', 'href="'.$targetBase.'/favicon.ico"', $html);

            // Thay thế các liên kết trang nội bộ
            $internalRoutes = [
                'san-pham', 'products', 'gioi-thieu', 'about',
                'ho-tro', 'support', 'contact', 'tin-tuc', 'news',
                'product', 'cart', 'checkout', 'login', 'register', 'forgot-password', 'orders', 'wishlist', 'profile', 'admin', 'email', 'reset-password',
            ];

            foreach ($internalRoutes as $r) {
                // Thay href="/san-pham" -> href="/lensStore/san-pham/"
                $html = preg_replace('/href="\/'.$r.'(\/[^"]*|\?|#[^"]*|)"/', 'href="'.$targetBase.'/'.$r.'$1"', $html);
                $html = preg_replace('/action="\/'.$r.'(\/[^"]*|\?|#[^"]*|)"/', 'action="'.$targetBase.'/'.$r.'$1"', $html);
            }

            // Thay link trang chủ href="/" -> href="/lensStore/"
            $html = preg_replace('/href="\/(#|\?|)"/', 'href="'.$targetBase.'/$1"', $html);
        }

        // 3. Đảm bảo các link thư mục trên GitHub Pages có trailing slash để tải index.html đúng
        $html = preg_replace('/href="('.preg_quote($targetBase, '/').'\/(?:gioi-thieu|about|ho-tro|support|contact|san-pham|products|tin-tuc|news|login|register|forgot-password|cart|checkout|wishlist|orders|profile|email|reset-password|admin(?:\/[a-zA-Z0-9_\-]+)*))"/', 'href="$1/"', $html);

        // 4. Nhúng Bộ điều khiển tương tác Client-Side tĩnh trước </body>
        $staticScript = $this->getStaticEnhancementsScript($basePrefix);
        $html = str_replace('</body>', $staticScript.'</body>', $html);

        return $html;
    }

    /**
     * Script hỗ trợ trải nghiệm tương tác tự nhiên, mượt mà trên môi trường tĩnh GitHub Pages.
     */
    protected function getStaticEnhancementsScript(string $basePrefix): string
    {
        $targetBase = ! empty($basePrefix) ? $basePrefix : '';
        $homeUrl = ! empty($targetBase) ? $targetBase.'/' : '/';

        return <<<HTML
<!-- LensStore Client-Side Static Interactive Engine -->
<script>
(function() {
    const BASE_URL = '{$targetBase}';
    const HOME_URL = '{$homeUrl}';

    // 1. Đồng bộ số lượng giỏ hàng trên Navbar từ localStorage
    function syncCartCount() {
        const count = parseInt(localStorage.getItem('ls_cart_count') || '1', 10);
        document.querySelectorAll('#cart-count, .sf-cart-dropdown span').forEach(el => {
            if (el) el.textContent = count;
        });
    }

    // 2. Tương tác Thêm vào giỏ hàng (.add-to-cart-btn)
    function initAddToCart() {
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                let currentCount = parseInt(localStorage.getItem('ls_cart_count') || '1', 10);
                currentCount += 1;
                localStorage.setItem('ls_cart_count', currentCount);
                syncCartCount();

                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-check"></i> Đã thêm';
                this.style.background = '#10b981';
                this.style.color = '#ffffff';

                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.style.background = '';
                    this.style.color = '';
                }, 1500);
            }, true);
        });
    }

    // 3. Tương tác Nút Yêu thích (.wishlist-btn)
    function initWishlist() {
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const icon = this.querySelector('i');
                if (icon) {
                    if (icon.classList.contains('fa-solid')) {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        icon.style.color = '';
                    } else {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        icon.style.color = '#ef4444';
                    }
                }
            });
        });
    }

    // 4. Tìm kiếm & Lọc sản phẩm trực tiếp (Live Product Filter) trên trang Sản phẩm
    function initLiveFilter() {
        const filterForm = document.querySelector('.filter-card');
        const productGrid = document.querySelector('.product-grid');
        if (!productGrid) return;

        const cards = Array.from(productGrid.querySelectorAll('.product-card'));
        const searchInput = document.querySelector('input[name="search"]');
        const categorySelect = document.querySelector('select[name="category"]');
        const brandSelect = document.querySelector('select[name="brand"]');
        const sortSelect = document.querySelector('select[name="sort"]');
        const countEl = document.querySelector('.products-count');

        function applyFilter() {
            const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
            const brandVal = (brandSelect ? brandSelect.value : '').trim().toUpperCase();
            const catVal = (categorySelect ? categorySelect.value : '').trim();

            let visibleCount = 0;
            cards.forEach(card => {
                const name = (card.querySelector('.product-name')?.textContent || '').toLowerCase();
                const brand = (card.querySelector('.badge-brand')?.textContent || '').toUpperCase();
                const cat = (card.querySelector('.product-cat')?.textContent || '').toLowerCase();

                let match = true;
                if (query && !name.includes(query) && !brand.toLowerCase().includes(query)) match = false;
                if (brandVal && brand !== brandVal) match = false;
                if (catVal && !cat.includes(catVal.toLowerCase())) match = false;

                if (match) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (countEl) countEl.textContent = visibleCount + ' sản phẩm';
        }

        // Đọc tham số URL ban đầu nếu có (vd ?search=canon)
        const params = new URLSearchParams(window.location.search);
        if (params.get('search') && searchInput) {
            searchInput.value = params.get('search');
            applyFilter();
        }
        if (params.get('brand') && brandSelect) {
            brandSelect.value = params.get('brand');
            applyFilter();
        }

        // Sự kiện lọc
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                applyFilter();
            });
        }
        if (searchInput) searchInput.addEventListener('input', applyFilter);
        if (categorySelect) categorySelect.addEventListener('change', applyFilter);
        if (brandSelect) brandSelect.addEventListener('change', applyFilter);
        if (sortSelect) sortSelect.addEventListener('change', applyFilter);

        // Click các chip thương hiệu CANON, SONY, NIKON...
        document.querySelectorAll('.brand-item').forEach(item => {
            item.style.cursor = 'pointer';
            item.addEventListener('click', function() {
                const brand = this.textContent.trim().toUpperCase();
                if (brandSelect) {
                    brandSelect.value = (brandSelect.value === brand) ? '' : brand;
                    applyFilter();
                }
            });
        });
    }

    // 5. Xử lý biểu mẫu POST tự nhiên & Phân luồng đăng nhập Admin vs Customer
    function initFormNavigation() {
        document.querySelectorAll('form').forEach(form => {
            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method !== 'POST') return; // Bỏ qua form GET

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = (form.getAttribute('action') || '').toLowerCase();

                if (action.includes('login') || form.querySelector('input[name="email"]')) {
                    const email = (form.querySelector('input[name="email"]')?.value || '').trim().toLowerCase();
                    if (email.includes('admin') || email === 'admin@example.com') {
                        // Quyền Quản trị viên -> Lưu role và chuyển thẳng vào Admin Dashboard
                        localStorage.setItem('ls_user_role', 'admin');
                        localStorage.setItem('ls_user_name', 'Administrator');
                        window.location.href = BASE_URL + '/admin/';
                    } else {
                        // Khách hàng thông thường -> Lưu role và chuyển vào Trang Khách hàng
                        localStorage.setItem('ls_user_role', 'customer');
                        localStorage.setItem('ls_user_name', 'Khách hàng');
                        window.location.href = BASE_URL + '/orders/';
                    }
                } else if (action.includes('register')) {
                    // Đăng ký -> Chuyển sang OTP Verify hoặc Đăng nhập
                    window.location.href = BASE_URL + '/email/verify/';
                } else if (action.includes('checkout') || form.id === 'checkout-form') {
                    // Đặt hàng -> Chuyển sang trang Đơn hàng
                    window.location.href = BASE_URL + '/orders/';
                } else if (action.includes('logout')) {
                    // Đăng xuất -> Xóa role và về Trang chủ
                    localStorage.removeItem('ls_user_role');
                    localStorage.removeItem('ls_user_name');
                    window.location.href = HOME_URL;
                } else if (action.includes('return')) {
                    // Đổi trả đơn hàng -> về trang đơn hàng
                    window.location.href = BASE_URL + '/orders/';
                } else if (action.includes('review')) {
                    // Đánh giá đơn hàng -> về trang đơn hàng
                    window.location.href = BASE_URL + '/orders/';
                } else if (action.includes('admin/products')) {
                    window.location.href = BASE_URL + '/admin/products/';
                } else if (action.includes('admin/categories')) {
                    window.location.href = BASE_URL + '/admin/categories/';
                } else if (action.includes('admin/vouchers')) {
                    window.location.href = BASE_URL + '/admin/vouchers/';
                } else if (action.includes('admin/news')) {
                    window.location.href = BASE_URL + '/admin/news/';
                } else if (action.includes('admin/roles')) {
                    window.location.href = BASE_URL + '/admin/roles/';
                } else if (action.includes('admin/users')) {
                    window.location.href = BASE_URL + '/admin/users/';
                } else if (action.includes('admin/goods_receipts')) {
                    window.location.href = BASE_URL + '/admin/goods_receipts/';
                } else if (action.includes('admin/goods_issues')) {
                    window.location.href = BASE_URL + '/admin/goods_issues/';
                } else if (action.includes('admin/qc_inspections')) {
                    window.location.href = BASE_URL + '/admin/qc_inspections/';
                } else {
                    // Các form khác: Phản hồi nút submit
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        const prev = submitBtn.textContent;
                        submitBtn.textContent = '✓ Hoàn tất thành công';
                        setTimeout(() => { submitBtn.textContent = prev; }, 2000);
                    }
                }
            });
        });
    }

    // 6. Đồng bộ trạng thái đăng nhập Navbar (Admin / Customer / Guest) trên toàn bộ trang
    function syncNavbarAuthState() {
        const role = localStorage.getItem('ls_user_role');
        const name = localStorage.getItem('ls_user_name') || (role === 'admin' ? 'Administrator' : 'Khách hàng');
        const authContainer = document.getElementById('sf-navbar-auth') || document.querySelector('.sf-nav-right, .navbar .nav-right, nav > div:last-child');
        if (!authContainer) return;

        // Nếu đã đăng nhập (role là admin hoặc customer)
        if (role === 'admin' || role === 'customer') {
            const guestBlock = authContainer.querySelector('.sf-guest-block');
            // Nếu container đang chứa nút Đăng nhập / Đăng ký
            if (guestBlock || authContainer.querySelector('a[href*="login"]') || authContainer.querySelector('a[href*="register"]')) {
                const adminLink = (role === 'admin')
                    ? `<a href="\${BASE_URL}/admin/" class="sf-admin-btn" style="text-decoration:none;color:var(--text-muted,#64748b);font-weight:500;padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;display:inline-flex;align-items:center;gap:6px;"><i class="fa-solid fa-gauge"></i> Quản lý</a>`
                    : '';
                const initial = name ? name.charAt(0).toUpperCase() : (role === 'admin' ? 'A' : 'K');
                const cartCount = parseInt(localStorage.getItem('ls_cart_count') || '1', 10);

                authContainer.innerHTML = `
                    \${adminLink}
                    <div class="sf-cart-dropdown" style="position:relative;display:inline-block;">
                        <a href="\${BASE_URL}/cart/" style="position:relative;cursor:pointer;text-decoration:none;color:var(--text-muted,#64748b);font-weight:500;padding:6px 12px;border-radius:8px;transition:all 0.2s;font-size:0.9rem;display:inline-flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
                            <span style="position:absolute;top:-4px;right:-2px;background:#ef4444;color:white;border-radius:50%;padding:1px 5px;font-size:0.65rem;font-weight:700;line-height:1.4;">\${cartCount}</span>
                        </a>
                    </div>
                    <a href="\${BASE_URL}/orders/" style="text-decoration:none;color:var(--text-muted,#64748b);font-weight:500;padding:6px 12px;border-radius:8px;font-size:0.9rem;"><i class="fa-solid fa-clipboard-list"></i> Đơn hàng</a>
                    <div class="sf-acc-dropdown" style="position:relative;display:inline-block;">
                        <a class="sf-acc-trigger" style="cursor:pointer;text-decoration:none;color:var(--text-muted,#64748b);font-weight:500;padding:6px 12px;border-radius:8px;font-size:0.9rem;display:inline-flex;align-items:center;gap:6px;user-select:none;">
                            <span style="width:30px;height:30px;background:var(--primary,#4f46e5);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8rem;">\${initial}</span>
                            <span>\${name}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;"></i>
                        </a>
                        <div class="sf-acc-menu" style="display:none;position:absolute;right:0;top:calc(100% + 6px);background:white;min-width:210px;box-shadow:0 20px 40px rgba(0,0,0,0.14);border-radius:14px;z-index:1000;border:1px solid var(--border,#e2e8f0);padding:8px 0;">
                            <a href="\${BASE_URL}/profile/" style="color:var(--text-main,#1e293b);display:flex;align-items:center;gap:10px;padding:10px 18px;text-decoration:none;font-size:0.9rem;"><i class="fa-solid fa-user" style="width:16px;color:var(--primary,#4f46e5);"></i> Hồ sơ cá nhân</a>
                            <a href="\${BASE_URL}/orders/" style="color:var(--text-main,#1e293b);display:flex;align-items:center;gap:10px;padding:10px 18px;text-decoration:none;font-size:0.9rem;"><i class="fa-solid fa-box" style="width:16px;color:var(--primary,#4f46e5);"></i> Đơn hàng của tôi</a>
                            <div style="height:1px;background:var(--border,#e2e8f0);margin:4px 0;"></div>
                            <a href="#" class="ls-logout-btn" style="color:#ef4444;display:flex;align-items:center;gap:10px;padding:10px 18px;text-decoration:none;font-size:0.9rem;font-weight:600;cursor:pointer;"><i class="fa-solid fa-right-from-bracket" style="width:16px;"></i> Đăng xuất</a>
                        </div>
                    </div>
                `;

                const accDropdown = authContainer.querySelector('.sf-acc-dropdown');
                const accMenu = authContainer.querySelector('.sf-acc-menu');
                const accTrigger = authContainer.querySelector('.sf-acc-trigger');
                if (accDropdown && accMenu) {
                    let closeTimer = null;
                    accDropdown.addEventListener('mouseenter', () => {
                        if (closeTimer) clearTimeout(closeTimer);
                        accMenu.style.display = 'block';
                    });
                    accDropdown.addEventListener('mouseleave', () => {
                        closeTimer = setTimeout(() => {
                            if (!accDropdown.classList.contains('is-open')) {
                                accMenu.style.display = 'none';
                            }
                        }, 250);
                    });
                    if (accTrigger) {
                        accTrigger.addEventListener('click', (e) => {
                            e.stopPropagation();
                            accDropdown.classList.toggle('is-open');
                            accMenu.style.display = accDropdown.classList.contains('is-open') ? 'block' : 'none';
                        });
                    }
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest('.sf-acc-dropdown')) {
                            accDropdown.classList.remove('is-open');
                            accMenu.style.display = 'none';
                        }
                    });
                }

                const logoutBtn = authContainer.querySelector('.ls-logout-btn');
                if (logoutBtn) {
                    logoutBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        localStorage.removeItem('ls_user_role');
                        localStorage.removeItem('ls_user_name');
                        window.location.href = HOME_URL;
                    });
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncNavbarAuthState();
        syncCartCount();
        initAddToCart();
        initWishlist();
        initLiveFilter();
        initFormNavigation();
    });
    // Kích hoạt ngay tức thì để chống nhấp nháy giao diện
    syncNavbarAuthState();
})();
</script>
HTML;
    }

    /**
     * Tạo trang 404.html thân thiện chuẩn SEO cho GitHub Pages.
     */
    protected function generate404Page(string $homeUrl): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang | LensStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
    (function() {
        var path = window.location.pathname;
        if (path.endsWith('/') && path.length > 1) {
            var stripped = path.slice(0, -1);
            if (stripped.endsWith('.html')) {
                window.location.replace(stripped);
            }
        }
    })();
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: white; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; text-align: center; }
        .card { max-width: 500px; padding: 3rem 2rem; background: rgba(255,255,255,0.05); border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); }
        h1 { font-size: 5rem; margin: 0; color: #4f46e5; font-weight: 800; }
        h2 { font-size: 1.5rem; margin: 1rem 0; }
        p { color: #94a3b8; line-height: 1.6; margin-bottom: 2rem; }
        a { background: #4f46e5; color: white; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s; }
        a:hover { background: #4338ca; }
    </style>
</head>
<body>
    <div class="card">
        <i class="fa-solid fa-camera-rotate" style="font-size: 3rem; color: #f59e0b; margin-bottom: 1rem;"></i>
        <h1>404</h1>
        <h2>Không Tìm Thấy Trang</h2>
        <p>Ống kính của bạn có vẻ đã lệch tiêu cự! Trang bạn đang tìm kiếm không tồn tại hoặc đã được chuyển hướng trên GitHub Pages.</p>
        <a href="{$homeUrl}"><i class="fa-solid fa-house"></i> Về Trang Chủ LensStore</a>
    </div>
</body>
</html>
HTML;
    }
}
