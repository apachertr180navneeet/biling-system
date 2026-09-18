<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_type_channel_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ota_channel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('ota_room_type_id')->nullable();
            $table->string('ota_room_name')->nullable();
            $table->decimal('rate_multiplier', 5, 2)->default(1.00);
            $table->boolean('sync_rates')->default(true);
            $table->boolean('sync_availability')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['ota_channel_id', 'room_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_type_channel_mappings');
    }
};
