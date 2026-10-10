<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'reason',
        'note',
        'image',
        'status',
        'tracking_code',
        'bank_name',
        'bank_account_holder',
        'bank_account_number',
        'refund_status',
        'refund_amount',
        'refunded_at',
        'refunded_by',
        'refund_method',
        'refund_reference',
        'refund_note',
    ];

    protected $casts = [
        'refund_amount' => 'integer',
        'refunded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã chấp nhận trả hàng',
            'rejected' => 'Từ chối yêu cầu',
            'completed' => 'Đã hoàn tất',
            default => ucfirst($this->status ?? ''),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => '#f59e0b',
            'approved' => '#10b981',
            'rejected' => '#ef4444',
            'completed' => '#059669',
            default => '#6b7280',
        };
    }

    public function getRefundStatusLabelAttribute(): string
    {
        return match ($this->refund_status) {
            'pending' => 'Chờ hoàn tiền',
            'processing' => 'Đang xử lý hoàn tiền',
            'refunded' => 'Đã hoàn tiền',
            'failed' => 'Hoàn tiền thất bại/Cần đối soát',
            default => ucfirst($this->refund_status ?? ''),
        };
    }

    public function getRefundStatusColorAttribute(): string
    {
        return match ($this->refund_status) {
            'pending' => '#f59e0b',
            'processing' => '#3b82f6',
            'refunded' => '#10b981',
            'failed' => '#ef4444',
            default => '#6b7280',
        };
    }

    /**
     * Đường dẫn truy cập ảnh bằng chứng hợp lệ
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset($this->image);
    }
}
