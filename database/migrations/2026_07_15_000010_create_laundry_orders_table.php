<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('order_number');
            $table->string('slug')->unique();
            $table->date('order_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->string('vendor_name')->nullable();
            $table->integer('total_items')->default(0);
            $table->decimal('total_weight', 8, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('order_status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'order_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};
