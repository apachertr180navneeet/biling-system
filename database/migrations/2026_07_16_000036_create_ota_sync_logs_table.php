<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ota_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ota_channel_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('action');
            $table->text('request_payload')->nullable();
            $table->text('response_payload')->nullable();
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['ota_channel_id', 'status']);
            $table->index(['reservation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ota_sync_logs');
    }
};
