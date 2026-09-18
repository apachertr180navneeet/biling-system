<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ota_channels', function (Blueprint $table) {
            $table->dropColumn('provider');
        });

        Schema::table('ota_channels', function (Blueprint $table) {
            $table->string('provider')->default('other')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('ota_channels', function (Blueprint $table) {
            $table->dropColumn('provider');
        });

        Schema::table('ota_channels', function (Blueprint $table) {
            $table->enum('provider', ['booking_com', 'expedia', 'other'])->default('other')->after('name');
        });
    }
};
