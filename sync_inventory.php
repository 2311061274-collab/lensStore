<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\GoodsIssue;
use App\Models\GoodsReceipt;
use App\Models\Product;

echo "Bat dau dong bo du lieu...\n";

// 1. Đồng bộ Xuất kho từ Đơn hàng cũ
$orders = Order::with('items')->whereIn('status', ['completed', 'finished'])->get();
$issueCount = 0;
foreach($orders as $order) {
    if(!GoodsIssue::where('order_id', $order->id)->exists()) {
        $issue = GoodsIssue::create([
            'order_id' => $order->id,
            'user_id' => 1, // Admin đầu tiên
            'type' => 'sale',
            'status' => 'completed',
            'note' => 'Đồng bộ lịch sử từ đơn hàng cũ #' . $order->id
        ]);
        foreach($order->items as $item) {
            $issue->details()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity
            ]);
        }
        $issueCount++;
    }
}
echo "Da dong bo $issueCount phieu Xuat Kho tu don hang cu.\n";

// 2. Tạo 1 Phiếu nhập kho gốc để khớp với số lượng tồn kho hiện tại
$products = Product::where('stock', '>', 0)->get();
if($products->count() > 0) {
    $totalAmount = 0;
    foreach($products as $p) {
        // Giả định giá vốn = 80% giá bán
        $totalAmount += ($p->price * 0.8 * $p->stock);
    }

    $receipt = GoodsReceipt::create([
        'receipt_code' => 'PNK-' . date('YmdHis') . '-SYNC',
        'supplier_id' => null,
        'user_id' => 1,
        'status' => 'completed',
        'note' => 'Chốt sổ tồn kho ban đầu (Đồng bộ)',
        'total_amount' => $totalAmount
    ]);

    foreach($products as $p) {
        $receipt->details()->create([
            'product_id' => $p->id,
            'quantity' => $p->stock,
            'unit_price' => $p->price * 0.8,
            'total_price' => $p->price * 0.8 * $p->stock
        ]);
    }
    echo "Da tao 1 Phieu Nhap Kho de can bang ton kho hien tai.\n";
}

echo "Xong!\n";
