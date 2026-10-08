<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$products = \App\Models\Product::all();
foreach ($products as $p) {
    echo $p->id . ' | ' . $p->name . ' | ' . $p->image . "\n";
}
