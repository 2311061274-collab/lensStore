<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount_value',
        'starts_at',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Kiểm tra voucher có hợp lệ để sử dụng hay không
     */
    public function isValidForOrder(float $orderValue): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < $now) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_order_value > 0 && $orderValue < $this->min_order_value) {
            return false;
        }

        return true;
    }

    /**
     * Tính số tiền được giảm
     */
    public function calculateDiscount(float $orderValue): float
    {
        if (!$this->isValidForOrder($orderValue)) {
            return 0;
        }

        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $orderValue);
        }

        // Percent
        $discount = $orderValue * ($this->discount_value / 100);
        
        if ($this->max_discount_value && $discount > $this->max_discount_value) {
            $discount = $this->max_discount_value;
        }

        return min($discount, $orderValue);
    }
}
