<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saving(function ($product) {
            if (empty($product->brand) && !empty($product->name)) {
                $product->brand = explode(' ', trim($product->name))[0];
            }
        });
    }

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'focal_length',
        'aperture',
        'mount',
        'price',
        'stock',
        'image',
        'description',
        'status',
        'brand',
        'brand_description',
        'gallery_images',
        'sample_images',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'gallery_images' => 'array',
        'sample_images' => 'array',
    ];

    /**
     * Danh mục của sản phẩm ống kính
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Định dạng giá tiền VNĐ
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy đường dẫn hình ảnh hoặc ảnh mặc định
     */
    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            return asset($this->image);
        }

        return 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=500&auto=format&fit=crop&q=80';
    }
}
