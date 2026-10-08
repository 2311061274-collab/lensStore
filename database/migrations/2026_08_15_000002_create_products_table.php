<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('focal_length')->nullable(); // Ví dụ: 24-70mm, 50mm, 70-200mm
            $table->string('aperture')->nullable();     // Ví dụ: f/2.8, f/1.4, f/1.2
            $table->string('mount')->nullable();        // Ví dụ: Sony E, Canon RF, Nikon Z, Fuji X
            $table->decimal('price', 15, 2)->default(0); // Giá bán VNĐ
            $table->integer('stock')->default(0);       // Số lượng tồn kho
            $table->string('image')->nullable();        // Đường dẫn ảnh sản phẩm
            $table->text('description')->nullable();    // Mô tả thông số quang học
            $table->string('status')->default('in_stock'); // in_stock, out_of_stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
