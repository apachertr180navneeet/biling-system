<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_sales_invoices', function (Blueprint $table) {
            $table->dropForeign(['vehicle_inventory_id']);
            $table->foreign('vehicle_inventory_id')->references('id')->on('vehicle_inventories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_sales_invoices', function (Blueprint $table) {
            $table->dropForeign(['vehicle_inventory_id']);
            $table->foreign('vehicle_inventory_id')->references('id')->on('vehicle_inventories')->cascadeOnDelete();
        });
    }
};
