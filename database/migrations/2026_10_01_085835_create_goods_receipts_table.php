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
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_code')->unique(); // Mã phiếu nhập
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete(); // NCC
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Nhân viên tạo phiếu
            $table->decimal('total_amount', 15, 2)->default(0); // Tổng tiền
            $table->text('note')->nullable(); // Ghi chú
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft'); // Trạng thái
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
