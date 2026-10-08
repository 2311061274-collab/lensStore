<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptDetail extends Model
{
    protected $fillable = ['goods_receipt_id', 'product_id', 'quantity', 'unit_price', 'total_price'];

    public function receipt() { return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id'); }
    public function product() { return $this->belongsTo(Product::class); }
}
