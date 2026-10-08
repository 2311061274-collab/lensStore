<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    protected $fillable = ['receipt_code', 'supplier_id', 'user_id', 'total_amount', 'note', 'status'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function details() { return $this->hasMany(GoodsReceiptDetail::class); }
}
