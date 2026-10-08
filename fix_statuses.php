<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

DB::update("update products set status = 'in_stock' where status = 'active'");
DB::update("update products set status = 'out_of_stock' where status = 'inactive'");
echo "Statuses updated.\n";
