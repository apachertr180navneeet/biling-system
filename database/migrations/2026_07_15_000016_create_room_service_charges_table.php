<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_service_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('charge_number');
            $table->string('slug')->unique();
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->foreignId('posted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('posted_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('charge_status', ['posted', 'voided'])->default('posted');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reservation_id', 'charge_status']);
            $table->index(['hotel_id', 'charge_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_service_charges');
    }
};
