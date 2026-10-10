<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcInspection extends Model
{
    use HasFactory;

    protected $fillable = ['return_request_id', 'product_id', 'user_id', 'condition', 'final_action', 'quantity', 'inspection_note'];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
