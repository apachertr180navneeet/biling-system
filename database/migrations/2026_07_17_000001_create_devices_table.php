<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['biometric', 'printer', 'smart_lock']);
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique();
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('api_key')->nullable();
            $table->json('settings')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'offline'])->default('active');
            $table->datetime('last_seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
