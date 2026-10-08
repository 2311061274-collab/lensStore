<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('cccd', 20)->nullable()->after('phone');
            $table->date('birthday')->nullable()->after('cccd');
            $table->string('address')->nullable()->after('birthday');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'cccd', 'birthday', 'address', 'gender']);
        });
    }
};
