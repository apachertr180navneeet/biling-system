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
        Schema::table('spare_sales', function (Blueprint $table) {
            $table->boolean('is_gst')->default(true)->after('sale_date');
            $table->string('gst_type')->nullable()->after('is_gst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spare_sales', function (Blueprint $table) {
            $table->dropColumn(['is_gst', 'gst_type']);
        });
    }
};
