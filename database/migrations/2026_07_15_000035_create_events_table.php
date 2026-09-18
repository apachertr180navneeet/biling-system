<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('hall_id')->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_number');
            $table->string('slug')->unique();
            $table->string('event_name');
            $table->enum('event_type', ['wedding', 'conference', 'seminar', 'exhibition', 'corporate', 'social', 'birthday', 'anniversary', 'other'])->default('other');
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('expected_guests', 8, 0)->default(0);
            $table->decimal('actual_guests', 8, 0)->nullable();
            $table->decimal('hall_charges', 12, 2)->default(0);
            $table->decimal('services_charges', 12, 2)->default(0);
            $table->decimal('additional_charges', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('advance_paid', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->text('special_requests')->nullable();
            $table->text('internal_notes')->nullable();
            $table->enum('booking_status', ['inquiry', 'proposed', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('inquiry');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'booking_status']);
            $table->index(['hall_id', 'event_date']);
            $table->index(['event_date', 'booking_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
