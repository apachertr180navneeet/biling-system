<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'vehicle_stock_id') && !Schema::hasColumn('invoices', 'vehicle_inventory_id')) {
                $table->foreignId('vehicle_inventory_id')->nullable()->constrained('vehicle_inventories')->nullOnDelete()->after('customer_id');
                DB::statement('UPDATE invoices SET vehicle_inventory_id = vehicle_stock_id');
                $table->dropForeign(['vehicle_stock_id']);
                $table->dropColumn('vehicle_stock_id');
            }
            if (!Schema::hasColumn('invoices', 'vehicle_description')) {
                $table->string('vehicle_description')->nullable()->after('customer_id');
                $table->string('chassis_number')->nullable()->after('vehicle_description');
                $table->string('engine_number')->nullable()->after('chassis_number');
                $table->string('mfg_year')->nullable()->after('engine_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['vehicle_description', 'chassis_number', 'engine_number', 'mfg_year']);
            $table->dropForeign(['vehicle_inventory_id']);
            $table->dropColumn('vehicle_inventory_id');
            $table->foreignId('vehicle_stock_id')->nullable()->constrained('vehicle_inventories')->nullOnDelete();
        });
    }
};
