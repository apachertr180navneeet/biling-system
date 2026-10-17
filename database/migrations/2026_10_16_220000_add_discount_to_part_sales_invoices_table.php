<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('part_sales_invoices') && !Schema::hasColumn('part_sales_invoices', 'discount')) {
            Schema::table('part_sales_invoices', function (Blueprint $table) {
                $table->decimal('discount', 12, 2)->default(0)->after('round_off');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('part_sales_invoices') && Schema::hasColumn('part_sales_invoices', 'discount')) {
            Schema::table('part_sales_invoices', function (Blueprint $table) {
                $table->dropColumn('discount');
            });
        }
    }
};
