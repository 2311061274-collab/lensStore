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
    protected $signature = 'app:export-static {--base-path= : Base URL path for GitHub Pages subfolder (e.g. /lar_demo)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Render and export LensStore storefront as a static site for GitHub Pages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $basePath = rtrim($this->option('base-path') ?? '', '/');
        $distDir = base_path('dist');

        $this->info("🚀 Bắt đầu xuất tĩnh website cho GitHub Pages (Base path: '{$basePath}')...");

        // 1. Dọn dẹp và tạo lại thư mục dist
        if (File::exists($distDir)) {
            File::deleteDirectory($distDir);
        }
        File::makeDirectory($distDir, 0755, true);

        // 2. Sao chép tài nguyên tĩnh (Vite build assets, uploads, images)
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

        // 3. Danh sách các trang tĩnh cần render
        $pages = [
            '/'           => 'index.html',
            '/san-pham'   => 'san-pham/index.html',
            '/gioi-thieu' => 'gioi-thieu/index.html',
            '/ho-tro'     => 'ho-tro/index.html',
            '/tin-tuc'    => 'tin-tuc/index.html',
        ];

        // Thêm chi tiết các sản phẩm ống kính
        try {
            $products = Product::all();
            foreach ($products as $prod) {
                $pages['/product/' . $prod->id] = 'product/' . $prod->id . '/index.html';
            }
            $this->info("   - Đã thu thập " . $products->count() . " sản phẩm ống kính.");
        } catch (\Throwable $e) {
            $this->warn("   ! Không thể đọc danh sách Product từ database: " . $e->getMessage());
        }

        // Thêm chi tiết các bài viết tin tức
        try {
            $newsArticles = News::all();
            foreach ($newsArticles as $article) {
                $pages['/tin-tuc/' . $article->id] = 'tin-tuc/' . $article->id . '/index.html';
            }
            $this->info("   - Đã thu thập " . $newsArticles->count() . " bài viết tin tức.");
        } catch (\Throwable $e) {
            $this->warn("   ! Không thể đọc danh sách News từ database: " . $e->getMessage());
        }

        // 4. Render từng trang và ghi vào file
        $this->info("📄 Đang render " . count($pages) . " trang...");
        $kernel = app()->make(\Illuminate\Contracts\Http\Kernel::class);

        foreach ($pages as $routePath => $outputRelativePath) {
            $outputFile = $distDir . '/' . $outputRelativePath;
            $outputSubDir = dirname($outputFile);
            if (!File::exists($outputSubDir)) {
                File::makeDirectory($outputSubDir, 0755, true);
            }

            try {
                $request = Request::create($routePath, 'GET');
                $response = $kernel->handle($request);
                $html = $response->getContent();
                $kernel->terminate($request, $response);

                // Xử lý base path cho GitHub Pages nếu có
                if (!empty($basePath)) {
                    $html = $this->adjustPathsForBasePath($html, $basePath);
                }

                File::put($outputFile, $html);
                $this->line("   ✓ Rendered: {$routePath} -> {$outputRelativePath}");
            } catch (\Throwable $e) {
                $this->error("   ✗ Lỗi khi render {$routePath}: " . $e->getMessage());
            }
        }

        // 5. Tạo 404.html dự phòng
        if (File::exists($distDir . '/index.html')) {
            File::copy($distDir . '/index.html', $distDir . '/404.html');
            $this->line("   - Đã tạo 404.html dự phòng");
        }

        $this->info("🎉 Xuất tĩnh thành công! Thư mục 'dist/' đã sẵn sàng cho GitHub Pages.");
        return Command::SUCCESS;
    }

    /**
     * Điều chỉnh các đường dẫn tuyệt đối bắt đầu bằng / sang {$basePath}/
     */
    protected function adjustPathsForBasePath(string $html, string $basePath): string
    {
        // Thay thế link assets Vite
        $html = str_replace('href="/build/', 'href="' . $basePath . '/build/', $html);
        $html = str_replace('src="/build/', 'src="' . $basePath . '/build/', $html);

        // Thay thế link uploads
        $html = str_replace('src="/uploads/', 'src="' . $basePath . '/uploads/', $html);
        $html = str_replace('href="/uploads/', 'href="' . $basePath . '/uploads/', $html);

        // Thay thế favicon & ảnh public
        $html = str_replace('href="/favicon.ico', 'href="' . $basePath . '/favicon.ico', $html);
        $html = str_replace('src="/images/', 'src="' . $basePath . '/images/', $html);

        // Thay thế các router link nội bộ
        $routes = [
            'href="/san-pham',
            'href="/product/',
            'href="/tin-tuc',
            'href="/gioi-thieu',
            'href="/ho-tro',
            'href="/cart',
            'href="/checkout',
            'href="/login',
            'href="/register',
        ];

        foreach ($routes as $route) {
            $prefix = substr($route, 6);
            $html = str_replace($route, 'href="' . $basePath . $prefix, $html);
        }

        return $html;
    }
}
