<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'recipient_name',
        'recipient_phone',
        'province_id',
        'province_name',
        'district_id',
        'district_name',
        'ward_code',
        'ward_name',
        'address_detail',
        'shipping_fee',
        'subtotal',
        'total',
        'payment_method',
        'status',
        'payment_status',
        'ghn_order_code',
        'voucher_code',
        'discount_amount',
    ];

    protected $casts = [
        'shipping_fee' => 'integer',
        'subtotal'     => 'integer',
        'total'        => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Chờ xác nhận',
            'preparing'  => 'Đang chuẩn bị hàng',
            'picked_up'  => 'Đơn vị vận chuyển đã lấy hàng',
            'delivering' => 'Đang giao hàng',
            'completed'  => 'Giao hàng thành công',
            'finished'   => 'Đã hoàn thành',
            'returning'  => 'Đang yêu cầu trả hàng / hoàn tiền',
            'returned'   => 'Đã trả hàng / hoàn tiền',
            'cancelled'  => 'Đã hủy',
            default      => match ($this->status) {
                'paid'   => 'Đã thanh toán',
                'unpaid' => 'Chưa thanh toán',
                default  => ucfirst($this->status ?? ''),
            },
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '#f59e0b',
            'preparing'  => '#3b82f6',
            'picked_up'  => '#8b5cf6',
            'delivering' => '#6366f1',
            'completed'  => '#10b981',
            'finished'   => '#059669',
            'returning'  => '#ea580c',
            'returned'   => '#64748b',
            'cancelled'  => '#ef4444',
            default      => '#6b7280',
        };
    }
}
