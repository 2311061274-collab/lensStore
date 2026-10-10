<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsIssue extends Model
{
    use HasFactory;

    protected $fillable = ['issue_code', 'order_id', 'user_id', 'type', 'status', 'note'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(GoodsIssueDetail::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->issue_code)) {
                $model->issue_code = 'PXK-'.date('YmdHis').'-'.rand(1000, 9999);
            }
        });
    }
}
