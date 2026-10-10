<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->boolean('is_active')->default(true)->after('description');
        });

        // Điền slug cho các categories đã tồn tại
        $categories = DB::table('categories')->get();
        foreach ($categories as $cat) {
            $slug = Str::slug($cat->name);
            $count = DB::table('categories')->where('slug', $slug)->where('id', '!=', $cat->id)->count();
            if ($count > 0) {
                $slug .= '-'.$cat->id;
            }
            DB::table('categories')->where('id', $cat->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_active']);
        });
    }
};
