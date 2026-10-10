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
            if (empty($product->brand) && ! empty($product->name)) {
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
        return number_format($this->price, 0, ',', '.').' ₫';
    }

    /**
     * Lấy đường dẫn hình ảnh chính hoặc ảnh mặc định chuẩn hóa
     */
    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            $img = trim($this->image);

            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                return $img;
            }

            if (str_starts_with($img, '//')) {
                return 'https:'.$img;
            }

            return asset(ltrim($img, '/'));
        }

        return 'https://images.unsplash.com/photo-1617005082133-548c4dd27f35?w=500&auto=format&fit=crop&q=80';
    }

    /**
     * Danh sách URL thư viện ảnh (Gallery) đã chuẩn hóa đầy đủ
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        $gallery = is_array($this->gallery_images) ? $this->gallery_images : [];
        $urls = [];

        foreach ($gallery as $item) {
            $rawUrl = is_array($item) ? ($item['url'] ?? '') : (is_string($item) ? $item : '');
            $rawUrl = trim($rawUrl);

            if (empty($rawUrl)) {
                continue;
            }

            if (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://')) {
                $urls[] = $rawUrl;
            } elseif (str_starts_with($rawUrl, '//')) {
                $urls[] = 'https:'.$rawUrl;
            } else {
                $urls[] = asset(ltrim($rawUrl, '/'));
            }
        }

        return $urls;
    }

    /**
     * Danh sách ảnh chụp mẫu (Sample Photos) với URL đã chuẩn hóa đầy đủ
     */
    public function getSampleImagesFormattedAttribute(): array
    {
        $samples = is_array($this->sample_images) ? $this->sample_images : [];
        $formatted = [];

        foreach ($samples as $item) {
            if (! is_array($item)) {
                continue;
            }

            $rawUrl = trim($item['url'] ?? '');
            if (empty($rawUrl)) {
                continue;
            }

            $finalUrl = $rawUrl;
            if (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://')) {
                $finalUrl = $rawUrl;
            } elseif (str_starts_with($rawUrl, '//')) {
                $finalUrl = 'https:'.$rawUrl;
            } else {
                $finalUrl = asset(ltrim($rawUrl, '/'));
            }

            $formatted[] = [
                'url' => $finalUrl,
                'tag' => $item['tag'] ?? '',
                'text' => $item['text'] ?? '',
            ];
        }

        return $formatted;
    }
}
