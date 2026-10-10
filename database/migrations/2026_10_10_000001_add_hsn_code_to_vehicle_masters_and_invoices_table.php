<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_masters') && !Schema::hasColumn('vehicle_masters', 'hsn_code')) {
            Schema::table('vehicle_masters', function (Blueprint $table) {
                $table->string('hsn_code', 20)->nullable()->default('8711')->after('variant_name');
            });
        }

        if (Schema::hasTable('vehicle_inventories') && !Schema::hasColumn('vehicle_inventories', 'hsn_code')) {
            Schema::table('vehicle_inventories', function (Blueprint $table) {
                $table->string('hsn_code', 20)->nullable()->default('8711')->after('vehicle_description');
            });
        }

        if (Schema::hasTable('vehicle_sales_invoices') && !Schema::hasColumn('vehicle_sales_invoices', 'hsn_code')) {
            Schema::table('vehicle_sales_invoices', function (Blueprint $table) {
                $table->string('hsn_code', 20)->nullable()->default('8711')->after('vehicle_inventory_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicle_masters') && Schema::hasColumn('vehicle_masters', 'hsn_code')) {
            Schema::table('vehicle_masters', function (Blueprint $table) {
                $table->dropColumn('hsn_code');
            });
        }

        if (Schema::hasTable('vehicle_inventories') && Schema::hasColumn('vehicle_inventories', 'hsn_code')) {
            Schema::table('vehicle_inventories', function (Blueprint $table) {
                $table->dropColumn('hsn_code');
            });
        }

        if (Schema::hasTable('vehicle_sales_invoices') && Schema::hasColumn('vehicle_sales_invoices', 'hsn_code')) {
            Schema::table('vehicle_sales_invoices', function (Blueprint $table) {
                $table->dropColumn('hsn_code');
            });
        }
    }
};
