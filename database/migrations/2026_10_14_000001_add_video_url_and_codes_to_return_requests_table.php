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
        Schema::table('return_requests', function (Blueprint $table) {
            $table->string('request_code', 50)->nullable()->unique()->after('id');
            $table->text('video_url')->nullable()->after('image');
            $table->string('return_shipping_status', 50)->default('pending_tracking')->after('tracking_code');
        });

        // Điền request_code và trạng thái vận chuyển cho các return_requests đã có sẵn
        $existing = DB::table('return_requests')->orderBy('id')->get();
        foreach ($existing as $req) {
            $code = 'RT-' . date('ymd', strtotime($req->created_at ?: now())) . '-' . str_pad($req->id, 4, '0', STR_PAD_LEFT);
            $shippingStatus = !empty($req->tracking_code) ? 'tracking_assigned' : 'pending_tracking';
            
            DB::table('return_requests')->where('id', $req->id)->update([
                'request_code' => $code,
                'return_shipping_status' => $shippingStatus,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['request_code', 'video_url', 'return_shipping_status']);
        });
    }
};
