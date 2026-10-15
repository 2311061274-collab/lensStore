<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mappings = [
            'Canon RF 50mm' => '/uploads/products/canon_rf_50_f12.jpg',
            'Sony FE 85mm' => '/uploads/products/sony_50_f12_gm.jpg',
            '7 kỹ thuật bố cục' => '/uploads/products/canon_rf_24_70.jpg',
            'Sigma 35mm' => '/uploads/products/sigma_24_70_art2.jpg',
            'Nikon Z 135mm' => '/uploads/products/nikon_z_85_f12.jpg',
            'Cách chọn ống kính' => '/uploads/products/nikon_z_14_24.jpg',
            'Tamron 150-500mm' => '/uploads/products/tamron_28_75_g2.jpg',
            'Hiểu về khẩu độ' => '/uploads/products/canon_rf_100_macro.jpg',
            'Fujifilm X100VI' => '/uploads/products/fuji_xf_56_f12.jpg',
            '10 lỗi phổ biến' => '/uploads/products/sony_70_200_gm2.jpg',
        ];

        foreach ($mappings as $titleKeyword => $imagePath) {
            DB::table('news')
                ->where('title', 'like', "%{$titleKeyword}%")
                ->update(['image_url' => $imagePath]);
        }

        // Với bất kỳ bản ghi nào còn lại có image_url trỏ tới /storage/ hoặc rỗng
        DB::table('news')
            ->whereNull('image_url')
            ->orWhere('image_url', '')
            ->orWhere('image_url', 'like', '/storage/%')
            ->update(['image_url' => '/uploads/products/canon_rf_50_f12.jpg']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback
    }
};
