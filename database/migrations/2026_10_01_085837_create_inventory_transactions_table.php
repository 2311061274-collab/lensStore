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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment', 'return']); // Loại giao dịch
            $table->integer('quantity'); // Số lượng thay đổi (dương hoặc âm)
            $table->string('reference_type')->nullable(); // Class model tham chiếu (GoodsReceipt, Order)
            $table->unsignedBigInteger('reference_id')->nullable(); // ID tham chiếu
            $table->text('note')->nullable(); // Lý do/Ghi chú
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
