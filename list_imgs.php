<?php

use App\Models\Product;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$products = Product::all();
foreach ($products as $p) {
    echo $p->id.' | '.$p->name.' | '.$p->image."\n";
}
