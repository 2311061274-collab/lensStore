<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReturnRequest extends Model
{
    protected $fillable = [
        'request_code',
        'user_id',
        'order_id',
        'reason',
        'note',
        'image',
        'video_url',
        'status',
        'tracking_code',
        'return_shipping_status',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_code)) {
                $model->request_code = 'RT-'.now()->format('ymd').'-'.strtoupper(Str::random(4));
            }
            if (empty($model->return_shipping_status)) {
                $model->return_shipping_status = ! empty($model->tracking_code) ? 'tracking_assigned' : 'pending_tracking';
            }
        });
    }

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

    public function getReturnShippingStatusLabelAttribute(): string
    {
        return match ($this->return_shipping_status) {
            'pending_tracking' => 'Chờ cấp mã vận đơn',
            'tracking_assigned' => 'Đã có mã vận đơn',
            'picking_up' => 'Đang lấy hàng',
            'delivering_back' => 'Đang vận chuyển về',
            'received' => 'Đã nhận hàng tại kho',
            default => 'Chờ cấp mã vận đơn',
        };
    }

    public function getReturnShippingStatusColorAttribute(): string
    {
        return match ($this->return_shipping_status) {
            'pending_tracking' => '#f59e0b',
            'tracking_assigned' => '#3b82f6',
            'picking_up' => '#8b5cf6',
            'delivering_back' => '#6366f1',
            'received' => '#10b981',
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

    /**
     * Chuyển đổi Google Drive link sang link preview/embed nếu có
     */
    public function getGoogleDrivePreviewUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        // Trường hợp link dạng /file/d/{ID}/view
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return "https://drive.google.com/file/d/{$matches[1]}/preview";
        }

        // Trường hợp link dạng open?id={ID} hoặc uc?id={ID}
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return "https://drive.google.com/file/d/{$matches[1]}/preview";
        }

        return $url;
    }
}
