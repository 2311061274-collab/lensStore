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
        // ALTER TABLE để thêm 'momo' vào ENUM
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod', 'bank_transfer', 'momo') NOT NULL DEFAULT 'cod'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `orders` MODIFY `payment_method` ENUM('cod', 'bank_transfer') NOT NULL DEFAULT 'cod'");
        }
    }
};
