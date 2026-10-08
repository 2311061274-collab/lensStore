<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

$products = Product::all();
$dir = public_path('uploads/products');
if (!File::isDirectory($dir)) {
    File::makeDirectory($dir, 0777, true, true);
}

foreach ($products as $p) {
    echo "Processing {$p->id} - {$p->name}\n";
    $query = urlencode($p->name . " lens product");
    $url = "https://images.search.yahoo.com/search/images?p={$query}";
    
    $html = Http::withoutVerifying()->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url)->body();
    
    if (preg_match('/imgurl=(http[^&]+)/', $html, $matches)) {
        $imgUrl = urldecode($matches[1]);
        echo "Found: $imgUrl\n";
        try {
            $imgData = Http::withoutVerifying()->withHeaders(['User-Agent' => 'Mozilla/5.0'])->timeout(10)->get($imgUrl)->body();
            if (strlen($imgData) > 1000) {
                $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                if (!$ext || strlen($ext) > 4) $ext = 'jpg';
                $filename = "lens_{$p->id}.{$ext}";
                
                File::put($dir . '/' . $filename, $imgData);
                $p->image = "uploads/products/{$filename}";
                $p->save();
                echo "Updated DB.\n";
            } else {
                echo "Invalid image data.\n";
            }
        } catch (\Exception $e) {
            echo "Download failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "No image found.\n";
    }
    sleep(1);
}
echo "Done.\n";
