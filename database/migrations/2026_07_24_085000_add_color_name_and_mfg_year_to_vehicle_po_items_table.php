<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_po_items', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_po_items', 'color_name')) {
                $table->string('color_name')->nullable()->after('vehicle_description');
            }
            if (!Schema::hasColumn('vehicle_po_items', 'mfg_year')) {
                $table->year('mfg_year')->nullable()->after('color_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_po_items', function (Blueprint $table) {
            if (Schema::hasColumn('vehicle_po_items', 'color_name')) {
                $table->dropColumn('color_name');
            }
            if (Schema::hasColumn('vehicle_po_items', 'mfg_year')) {
                $table->dropColumn('mfg_year');
            }
        });
    }
};
