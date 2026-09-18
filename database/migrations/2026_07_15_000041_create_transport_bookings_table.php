<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('transport_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->string('booking_number');
            $table->string('slug')->unique();
            $table->string('guest_name');
            $table->string('guest_phone');
            $table->string('guest_email')->nullable();
            $table->enum('trip_type', ['pickup', 'drop', 'round_trip', 'hourly'])->default('pickup');
            $table->string('pickup_location');
            $table->string('drop_location');
            $table->datetime('pickup_datetime');
            $table->datetime('drop_datetime')->nullable();
            $table->decimal('estimated_distance_km', 8, 2)->nullable();
            $table->decimal('estimated_hours', 5, 2)->nullable();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('distance_charges', 10, 2)->default(0);
            $table->decimal('hourly_charges', 10, 2)->default(0);
            $table->decimal('additional_charges', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('advance_paid', 10, 2)->default(0);
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('special_instructions')->nullable();
            $table->string('status')->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('is_room_charge', ['yes', 'no'])->default('no');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
            $table->index(['pickup_datetime', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_bookings');
    }
};
