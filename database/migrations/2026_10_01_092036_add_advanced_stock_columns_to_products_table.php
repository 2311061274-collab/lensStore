<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('reserved_stock')->default(0)->after('stock');
            $table->integer('quarantine_stock')->default(0)->after('reserved_stock');
            $table->integer('defective_stock')->default(0)->after('quarantine_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['reserved_stock', 'quarantine_stock', 'defective_stock']);
        });
    }
};
