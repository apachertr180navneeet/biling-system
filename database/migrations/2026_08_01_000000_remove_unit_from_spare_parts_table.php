<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            if (Schema::hasColumn('spare_parts', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->string('unit', 10)->default('pcs')->after('mrp');
        });
    }
};
