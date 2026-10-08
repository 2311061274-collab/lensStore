<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$urls = [
    "https://images.unsplash.com/photo-1516961642265-531546e84af2?w=600&auto=format&fit=crop&q=80", // Lens 1
    "https://images.unsplash.com/photo-1505691723518-36a5ac3be353?w=600&auto=format&fit=crop&q=80", // Lens 2
    "https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=80", // Lens 3
    "https://images.unsplash.com/photo-1495707902641-75cac588d2e9?w=600&auto=format&fit=crop&q=80", // Lens 4
    "https://images.unsplash.com/photo-1502982720700-bfff97f2da8d?w=600&auto=format&fit=crop&q=80", // Lens 5
    "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=600&auto=format&fit=crop&q=80", // Lens 6
    "https://images.unsplash.com/photo-1590291103653-997f5deee922?w=600&auto=format&fit=crop&q=80", // Lens 7
    "https://images.unsplash.com/photo-1512790182412-b19e6d62bc39?w=600&auto=format&fit=crop&q=80", // Lens 8
    "https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&auto=format&fit=crop&q=80", // Lens 9
    "https://images.unsplash.com/photo-1507646227500-4d389b0012be?w=600&auto=format&fit=crop&q=80", // Lens 10
    "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&auto=format&fit=crop&q=80", // Lens 11
    "https://images.unsplash.com/photo-1510127031490-453be2b45466?w=600&auto=format&fit=crop&q=80", // Lens 12
    "https://images.unsplash.com/photo-1586202476566-3d71ff03b578?w=600&auto=format&fit=crop&q=80", // Lens 13
    "https://images.unsplash.com/photo-1520110120835-c96534a4c984?w=600&auto=format&fit=crop&q=80", // Lens 14
    "https://images.unsplash.com/photo-1500462918059-b1a0cb512f1d?w=600&auto=format&fit=crop&q=80"  // Lens 15
];

$products = Product::all();
$i = 0;
foreach ($products as $p) {
    $p->image = $urls[$i % count($urls)];
    $p->save();
    $i++;
}

echo "Assigned " . $i . " realistic lens placeholders.\n";
