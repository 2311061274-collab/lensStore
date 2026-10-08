<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Thông tin người nhận
            $table->string('recipient_name');
            $table->string('recipient_phone');

            // Địa chỉ giao hàng (GHN)
            $table->integer('province_id');
            $table->string('province_name');
            $table->integer('district_id');
            $table->string('district_name');
            $table->string('ward_code');
            $table->string('ward_name');
            $table->string('address_detail');  // Số nhà, tên đường

            // Phí vận chuyển & thanh toán
            $table->unsignedBigInteger('shipping_fee')->default(0);  // đơn vị: VND
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total');
            $table->enum('payment_method', ['cod', 'bank_transfer'])->default('cod');

            // Trạng thái đơn hàng
            $table->enum('status', [
                'pending',
                'processing',
                'shipping',
                'delivered',
                'cancelled',
            ])->default('pending');

            // Thông tin GHN
            $table->string('ghn_order_code')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
