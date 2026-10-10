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
        Schema::table('return_requests', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('note');
            $table->string('bank_account_holder')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_holder');
            $table->string('refund_status')->default('pending')->after('status'); // pending, processing, refunded, failed
            $table->unsignedBigInteger('refund_amount')->nullable()->after('refund_status');
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            $table->foreignId('refunded_by')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
            $table->string('refund_method')->default('bank_transfer')->nullable()->after('refunded_by');
            $table->string('refund_reference')->nullable()->after('refund_method');
            $table->text('refund_note')->nullable()->after('refund_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropForeign(['refunded_by']);
            $table->dropColumn([
                'bank_name',
                'bank_account_holder',
                'bank_account_number',
                'refund_status',
                'refund_amount',
                'refunded_at',
                'refunded_by',
                'refund_method',
                'refund_reference',
                'refund_note',
            ]);
        });
    }
};
