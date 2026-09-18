<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('ota_channel_id')->nullable()->constrained('ota_channels')->onDelete('set null')->after('ota_name');
            $table->string('ota_reservation_id')->nullable()->after('ota_channel_id');
            $table->string('ota_reservation_status')->nullable()->after('ota_reservation_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['ota_channel_id']);
            $table->dropColumn(['ota_channel_id', 'ota_reservation_id', 'ota_reservation_status']);
        });
    }
};
