<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->text('vehicle_description')->nullable();
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->date('booking_date');
            $table->decimal('booking_amount', 12, 2)->default(0);
            $table->date('allotment_date')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('reg_number')->nullable();
            $table->date('delivery_date')->nullable();
            $table->enum('status', ['booking', 'allotment', 'registration', 'delivery', 'completed'])->default('booking');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
