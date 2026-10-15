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
            'Canon RF 50mm' => '/uploads/products/1790239840_6ab4e4606a454.jpg',
            'Sony FE 85mm' => '/uploads/products/1790238927_6ab4e0cfb31a4.jpg',
            '7 kỹ thuật bố cục' => '/uploads/products/1790239745_6ab4e4017e75b.jpg',
            'Sigma 35mm' => '/uploads/products/1790239920_6ab4e4b09fb1b.webp',
            'Nikon Z 135mm' => '/uploads/products/1790239033_6ab4e13977f56.jpg',
            'Cách chọn ống kính' => '/uploads/products/1790239894_6ab4e496c050f.png',
            'Tamron 150-500mm' => '/uploads/products/1790239588_6ab4e364e6779.jpg',
            'Hiểu về khẩu độ' => '/uploads/products/1790238799_6ab4e04f0d3bd.jpg',
            'Fujifilm X100VI' => '/uploads/products/1790239680_6ab4e3c0e3fa1.webp',
            '10 lỗi phổ biến' => '/uploads/products/1790239717_6ab4e3e58da2f.png',
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
            ->update(['image_url' => '/uploads/products/1790239840_6ab4e4606a454.jpg']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback
    }
};
