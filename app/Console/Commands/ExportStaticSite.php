<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Product;
use App\Models\News;

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
        $basePrefix = !empty($rawBasePath) ? '/' . trim($rawBasePath, '/') : '';
        $distDir = base_path('dist');

        $this->info("🚀 Bắt đầu xuất tĩnh website cho GitHub Pages (Base prefix: '{$basePrefix}')...");

        // 1. Dọn dẹp và tạo lại thư mục dist
        if (File::exists($distDir)) {
            File::deleteDirectory($distDir);
        }
        File::makeDirectory($distDir, 0755, true);

        // 2. Sao chép tài nguyên tĩnh (Vite build assets, uploads, images, favicon)
        $this->info("📦 Đang sao chép assets...");
        
        if (File::exists(public_path('build'))) {
            File::copyDirectory(public_path('build'), $distDir . '/build');
            $this->line("   - Đã sao chép public/build");
        } else {
            $this->warn("   ! Cảnh báo: public/build chưa tồn tại. Hãy chạy 'npm run build' trước.");
        }

        if (File::exists(public_path('uploads'))) {
            File::copyDirectory(public_path('uploads'), $distDir . '/uploads');
            $this->line("   - Đã sao chép public/uploads");
        }

        if (File::exists(public_path('images'))) {
            File::copyDirectory(public_path('images'), $distDir . '/images');
            $this->line("   - Đã sao chép public/images");
        }

        if (File::exists(public_path('favicon.ico'))) {
            File::copy(public_path('favicon.ico'), $distDir . '/favicon.ico');
        }

        // Tạo file .nojekyll để GitHub Pages không dùng Jekyll xử lý các file _
        File::put($distDir . '/.nojekyll', '');
        $this->line("   - Đã tạo .nojekyll");

        // 3. Danh sách các trang tĩnh cần render (Key: Route path, Value: slug)
        $publicPages = [
            '/'                 => '',
            '/san-pham'         => 'san-pham',
            '/products'         => 'products',
            '/gioi-thieu'       => 'gioi-thieu',
            '/about'            => 'about',
            '/ho-tro'           => 'ho-tro',
            '/support'          => 'support',
            '/contact'          => 'contact',
            '/tin-tuc'          => 'tin-tuc',
            '/news'             => 'news',
            '/login'            => 'login',
            '/register'         => 'register',
            '/forgot-password'  => 'forgot-password',
        ];

        // Trang trải nghiệm giỏ hàng & tài khoản (yêu cầu session auth để render đầy đủ)
        $authPages = [
            '/cart'             => 'cart',
            '/checkout'         => 'checkout',
            '/wishlist'         => 'wishlist',
            '/orders'           => 'orders',
            '/profile'          => 'profile',
        ];

        // Thu thập toàn bộ sản phẩm ống kính từ database
        try {
            $products = Product::all();
            foreach ($products as $prod) {
                $publicPages['/product/' . $prod->id] = 'product/' . $prod->id;
                $publicPages['/san-pham/' . $prod->id] = 'san-pham/' . $prod->id;
            }
            $this->info("   - Đã thu thập " . $products->count() . " sản phẩm ống kính.");
        } catch (\Throwable $e) {
            $this->warn("   ! Không thể đọc danh sách Product từ database: " . $e->getMessage());
        }

        // Thu thập toàn bộ bài viết tin tức từ database
        try {
            $newsArticles = News::all();
            foreach ($newsArticles as $article) {
                $publicPages['/tin-tuc/' . $article->id] = 'tin-tuc/' . $article->id;
                $publicPages['/news/' . $article->id] = 'news/' . $article->id;
            }
            $this->info("   - Đã thu thập " . $newsArticles->count() . " bài viết tin tức.");
        } catch (\Throwable $e) {
            $this->warn("   ! Không thể đọc danh sách News từ database: " . $e->getMessage());
        }

        // Chuẩn bị tài khoản và giỏ hàng mẫu để trang Cart & Checkout render trọn vẹn dữ liệu
        $demoUser = null;
        try {
            $demoUser = \App\Models\User::first();
            if ($demoUser) {
                $sampleProduct = Product::first();
                if ($sampleProduct) {
                    \App\Models\Cart::updateOrCreate(
                        ['user_id' => $demoUser->id, 'product_id' => $sampleProduct->id],
                        ['quantity' => 1, 'is_selected' => true]
                    );
                }
            }
        } catch (\Throwable $e) {
            $this->warn("   ! Không thể nạp Demo User: " . $e->getMessage());
        }

        // Ghép toàn bộ trang để render
        $allPages = [];
        foreach ($publicPages as $routePath => $slug) {
            $allPages[$routePath] = ['slug' => $slug, 'auth' => false];
        }
        foreach ($authPages as $routePath => $slug) {
            $allPages[$routePath] = ['slug' => $slug, 'auth' => true];
        }

        // 4. Render từng trang và ghi tệp Dual-Path
        $this->info("📄 Đang render và chuẩn hóa " . count($allPages) . " trang cho GitHub Pages...");
        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        foreach ($allPages as $routePath => $pageConfig) {
            $slug = $pageConfig['slug'];
            $needsAuth = $pageConfig['auth'];
            try {
                $request = Request::create($routePath, 'GET');
                app()->instance('request', $request);

                if ($needsAuth && $demoUser) {
                    auth()->login($demoUser);
                } else {
                    auth()->logout();
                }

                $response = $kernel->handle($request);
                $html = $response->getContent();
                $kernel->terminate($request, $response);

                // Chuẩn hóa toàn bộ URL, triệt tiêu localhost và chuyển sang basePrefix
                $html = $this->normalizeHtmlForGitHubPages($html, $basePrefix);

                // Ghi file theo Dual-Path
                if ($slug === '') {
                    // Trang chủ: index.html
                    File::put($distDir . '/index.html', $html);
                    $this->line("   ✓ Rendered: [Home] -> index.html");
                } else {
                    // 1. Dạng thư mục có index.html: vd dist/gioi-thieu/index.html (cho URL .../gioi-thieu/)
                    $subDir = $distDir . '/' . $slug;
                    if (!File::exists($subDir)) {
                        File::makeDirectory($subDir, 0755, true);
                    }
                    File::put($subDir . '/index.html', $html);

                    // 2. Dạng tệp .html đơn lẻ: vd dist/gioi-thieu.html (cho URL .../gioi-thieu)
                    $singleFile = $distDir . '/' . $slug . '.html';
                    $parentDir = dirname($singleFile);
                    if (!File::exists($parentDir)) {
                        File::makeDirectory($parentDir, 0755, true);
                    }
                    File::put($singleFile, $html);

                    $this->line("   ✓ Rendered: {$routePath} -> {$slug}/index.html & {$slug}.html");
                }
            } catch (\Throwable $e) {
                $this->error("   ✗ Lỗi khi render {$routePath}: " . $e->getMessage());
            }
        }

        // Reset auth state sau khi render
        try {
            auth()->logout();
        } catch (\Throwable $e) {}

        // 5. Tạo 404.html chuyên nghiệp cho GitHub Pages
        $homeUrl = !empty($basePrefix) ? $basePrefix . '/' : '/';
        $notFoundHtml = $this->generate404Page($homeUrl);
        File::put($distDir . '/404.html', $notFoundHtml);
        $this->line("   - Đã tạo 404.html thân thiện cho GitHub Pages");

        $this->info("🎉 Xuất tĩnh hoàn tất 100%! Đã xử lý triệt để link localhost, hỗ trợ Dual-Path và Demo Mode.");
        return Command::SUCCESS;
    }

    /**
     * Chuẩn hóa toàn diện mã HTML để tương thích tuyệt đối với GitHub Pages.
     */
    protected function normalizeHtmlForGitHubPages(string $html, string $basePrefix): string
    {
        $targetBase = !empty($basePrefix) ? $basePrefix : '';
        $homeUrl = !empty($targetBase) ? $targetBase . '/' : '/';

        // 1. Xóa bỏ hoàn toàn mọi URL tuyệt đối localhost
        $localhostPatterns = [
            'http://localhost:8000',
            'https://localhost:8000',
            'http://127.0.0.1:8000',
            'https://127.0.0.1:8000',
            'http://localhost',
            'https://localhost',
            'http://127.0.0.1',
            'https://127.0.0.1',
        ];

        foreach ($localhostPatterns as $lh) {
            $html = str_replace($lh . '/', $targetBase . '/', $html);
            $html = str_replace($lh, $targetBase . '/', $html);
        }

        // 2. Xử lý các liên kết tương đối bắt đầu bằng /
        if (!empty($targetBase)) {
            // Thay thế liên kết assets
            $html = preg_replace('/href="\/(build\/[^"]*)"/', 'href="' . $targetBase . '/$1"', $html);
            $html = preg_replace('/src="\/(build\/[^"]*)"/', 'src="' . $targetBase . '/$1"', $html);
            $html = preg_replace('/src="\/(uploads\/[^"]*)"/', 'src="' . $targetBase . '/$1"', $html);
            $html = preg_replace('/href="\/(uploads\/[^"]*)"/', 'href="' . $targetBase . '/$1"', $html);
            $html = preg_replace('/src="\/(images\/[^"]*)"/', 'src="' . $targetBase . '/$1"', $html);
            $html = str_replace('href="/favicon.ico"', 'href="' . $targetBase . '/favicon.ico"', $html);

            // Thay thế các liên kết trang nội bộ
            $internalRoutes = [
                'san-pham', 'products', 'gioi-thieu', 'about',
                'ho-tro', 'support', 'contact', 'tin-tuc', 'news',
                'product', 'cart', 'checkout', 'login', 'register', 'forgot-password', 'orders', 'wishlist', 'profile', 'admin'
            ];

            foreach ($internalRoutes as $r) {
                // Thay href="/san-pham" -> href="/lensStore/san-pham/"
                $html = preg_replace('/href="\/' . $r . '(\/[^"]*|\?|#[^"]*|)"/', 'href="' . $targetBase . '/' . $r . '$1"', $html);
                $html = preg_replace('/action="\/' . $r . '(\/[^"]*|\?|#[^"]*|)"/', 'action="' . $targetBase . '/' . $r . '$1"', $html);
            }

            // Thay link trang chủ href="/" -> href="/lensStore/"
            $html = preg_replace('/href="\/(#|\?|)"/', 'href="' . $targetBase . '/$1"', $html);
        }

        // 3. Đảm bảo các link thư mục trên GitHub Pages có trailing slash để tải index.html đúng
        $html = preg_replace('/href="(' . preg_quote($targetBase, '/') . '\/(?:gioi-thieu|about|ho-tro|support|contact|san-pham|products|tin-tuc|news|login|register|forgot-password|cart|checkout|wishlist|orders|profile))"/', 'href="$1/"', $html);

        // 4. Nhúng Bộ điều khiển tương tác Client-Side tĩnh trước </body>
        $staticScript = $this->getStaticEnhancementsScript($basePrefix);
        $html = str_replace('</body>', $staticScript . '</body>', $html);

        return $html;
    }

    /**
     * Script hỗ trợ trải nghiệm tương tác tự nhiên, mượt mà trên môi trường tĩnh GitHub Pages.
     */
    protected function getStaticEnhancementsScript(string $basePrefix): string
    {
        $targetBase = !empty($basePrefix) ? $basePrefix : '';
        $homeUrl = !empty($targetBase) ? $targetBase . '/' : '/';

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

    // 5. Xử lý biểu mẫu POST tự nhiên, không gây lỗi 405 trên GitHub Pages
    function initFormNavigation() {
        document.querySelectorAll('form').forEach(form => {
            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method !== 'POST') return; // Bỏ qua form GET

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = form.getAttribute('action') || '';

                if (action.includes('login')) {
                    // Đăng nhập -> Chuyển về Trang chủ
                    window.location.href = HOME_URL;
                } else if (action.includes('register')) {
                    // Đăng ký -> Chuyển sang Đăng nhập
                    window.location.href = BASE_URL + '/login';
                } else if (action.includes('checkout') || form.id === 'checkout-form') {
                    // Đặt hàng -> Chuyển sang trang Đơn hàng
                    window.location.href = BASE_URL + '/orders';
                } else if (action.includes('logout')) {
                    // Đăng xuất -> Về Trang chủ
                    window.location.href = HOME_URL;
                } else {
                    // Các form khác (newsletter footer, v.v.): Phản hồi nút submit
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        const prev = submitBtn.textContent;
                        submitBtn.textContent = '✓ Hoàn tất';
                        setTimeout(() => { submitBtn.textContent = prev; }, 2000);
                    }
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncCartCount();
        initAddToCart();
        initWishlist();
        initLiveFilter();
        initFormNavigation();
    });
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
