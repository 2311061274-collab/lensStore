<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $fillable = ['user_id', 'order_id', 'reason', 'note', 'image', 'status', 'tracking_code'];

    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Chờ duyệt',
            'approved' => 'Đã duyệt hoàn tiền',
            'rejected' => 'Từ chối yêu cầu',
            default    => ucfirst($this->status ?? ''),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '#f59e0b',
            'approved' => '#10b981',
            'rejected' => '#ef4444',
            default    => '#6b7280',
        };
    }
}
