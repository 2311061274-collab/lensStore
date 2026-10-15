<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $guarded = [];

    /**
     * Tự động xử lý đường dẫn ảnh tin tức, ưu tiên ảnh có sẵn trong dự án và fallback an toàn.
     */
    public function getImageUrlAttribute($value): string
    {
        // 1. Nếu rỗng, dùng ảnh mặc định có sẵn trong public/uploads/products/
        if (empty($value)) {
            return asset('uploads/products/canon_rf_50_f12.jpg');
        }

        // 2. Nếu là URL đầy đủ từ bên ngoài
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // 3. Nếu là đường dẫn trong public/storage/ (thư mục bị gitignore) hoặc không tồn tại file thực tế
        if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
            $storageRelative = ltrim($value, '/');
            if (! file_exists(public_path($storageRelative))) {
                return $this->resolveLocalFallbackImage();
            }
        }

        // 4. Nếu là đường dẫn bắt đầu bằng /uploads/ hoặc uploads/
        if (str_starts_with($value, '/uploads/') || str_starts_with($value, 'uploads/')) {
            return asset(ltrim($value, '/'));
        }

        return $value;
    }

    /**
     * Chọn ảnh thay thế phù hợp nhất có sẵn trong thư mục public/uploads/products/
     */
    public function resolveLocalFallbackImage(): string
    {
        $titleLower = mb_strtolower($this->title ?? '');

        if (str_contains($titleLower, 'canon') || str_contains($titleLower, '50mm')) {
            return asset('uploads/products/canon_rf_50_f12.jpg');
        }
        if (str_contains($titleLower, 'sony') || str_contains($titleLower, '85mm')) {
            return asset('uploads/products/sony_50_f12_gm.jpg');
        }
        if (str_contains($titleLower, 'nikon') || str_contains($titleLower, 'plena')) {
            return asset('uploads/products/nikon_z_85_f12.jpg');
        }
        if (str_contains($titleLower, 'sigma')) {
            return asset('uploads/products/sigma_24_70_art2.jpg');
        }
        if (str_contains($titleLower, 'tamron')) {
            return asset('uploads/products/tamron_28_75_g2.jpg');
        }
        if (str_contains($titleLower, 'fuji')) {
            return asset('uploads/products/fuji_xf_56_f12.jpg');
        }
        if (str_contains($titleLower, 'bố cục') || str_contains($titleLower, 'kỹ thuật')) {
            return asset('uploads/products/canon_rf_24_70.jpg');
        }
        if (str_contains($titleLower, 'khẩu độ') || str_contains($titleLower, 'macro')) {
            return asset('uploads/products/canon_rf_100_macro.jpg');
        }

        return asset('uploads/products/canon_rf_50_f12.jpg');
    }
}
