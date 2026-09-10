<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_sales_invoices') && !Schema::hasColumn('vehicle_sales_invoices', 'customer_gstin')) {
            Schema::table('vehicle_sales_invoices', function (Blueprint $table) {
                $table->string('customer_gstin', 15)->nullable()->after('customer_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicle_sales_invoices') && Schema::hasColumn('vehicle_sales_invoices', 'customer_gstin')) {
            Schema::table('vehicle_sales_invoices', function (Blueprint $table) {
                $table->dropColumn('customer_gstin');
            });
        }
    }
};
