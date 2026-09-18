<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ota_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('provider', ['booking_com', 'expedia', 'other'])->default('other');
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->string('property_id_on_ota')->nullable();
            $table->string('endpoint_url')->nullable();
            $table->boolean('sync_rates')->default(true);
            $table->boolean('sync_availability')->default(true);
            $table->boolean('sync_reservations')->default(true);
            $table->boolean('auto_sync')->default(false);
            $table->timestamp('last_synced_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ota_channels');
    }
};
