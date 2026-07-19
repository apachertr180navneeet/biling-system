<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('vehicle_stock_id', 'vehicle_inventory_id');
            $table->string('vehicle_description')->nullable()->after('customer_id');
            $table->string('chassis_number')->nullable()->after('vehicle_description');
            $table->string('engine_number')->nullable()->after('chassis_number');
            $table->string('mfg_year')->nullable()->after('engine_number');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['vehicle_description', 'chassis_number', 'engine_number', 'mfg_year']);
            $table->renameColumn('vehicle_inventory_id', 'vehicle_stock_id');
        });
    }
};
