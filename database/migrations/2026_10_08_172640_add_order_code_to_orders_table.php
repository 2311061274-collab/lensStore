<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code')->nullable()->after('id');
        });

        // Update existing orders with a generated code
        $orders = DB::table('orders')->get();
        foreach ($orders as $order) {
            $datePart = Carbon::parse($order->created_at)->format('ymd');
            $idPart = str_pad($order->id, 4, '0', STR_PAD_LEFT);
            DB::table('orders')->where('id', $order->id)->update([
                'order_code' => "LENS-{$datePart}-{$idPart}",
            ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_code');
        });
    }
};
