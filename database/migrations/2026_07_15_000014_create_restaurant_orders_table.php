<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('order_number');
            $table->string('slug')->unique();
            $table->foreignId('restaurant_table_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('reservation_id')->nullable()->constrained()->onDelete('set null');
            $table->string('guest_name')->nullable();
            $table->enum('order_type', ['dine_in', 'takeaway', 'room_service'])->default('dine_in');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'partially_paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->enum('order_status', ['pending', 'preparing', 'ready', 'served', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'order_status']);
            $table->index(['restaurant_table_id', 'order_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_orders');
    }
};
