<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ALTER TABLE để thêm 'momo' vào ENUM
        DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod', 'bank_transfer', 'momo') NOT NULL DEFAULT 'cod'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod', 'bank_transfer') NOT NULL DEFAULT 'cod'");
    }
};
