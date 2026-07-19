<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_cards', function (Blueprint $table) {
            $table->id();
            $table->string('job_card_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('kilometer_reading')->nullable();
            $table->text('complaint')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'billed'])->default('pending');
            $table->decimal('total_labor', 12, 2)->default(0);
            $table->decimal('total_parts', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->boolean('is_gst')->default(false);
            $table->enum('gst_type', ['cgst_sgst', 'igst'])->nullable();
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('cess_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->date('service_date');
            $table->date('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_cards');
    }
};
