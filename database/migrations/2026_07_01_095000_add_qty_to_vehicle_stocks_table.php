<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_stocks', function (Blueprint $table) {
            $table->integer('qty')->default(1)->after('id');
        });

        Schema::table('vehicle_stocks', function (Blueprint $table) {
            $table->dropUnique(['chassis_number']);
        });

        Schema::table('vehicle_stocks', function (Blueprint $table) {
            $table->string('chassis_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_stocks', function (Blueprint $table) {
            $table->dropColumn('qty');
        });

        Schema::table('vehicle_stocks', function (Blueprint $table) {
            $table->string('chassis_number')->nullable(false)->change();
        });

        Schema::table('vehicle_stocks', function (Blueprint $table) {
            $table->unique('chassis_number');
        });
    }
};
