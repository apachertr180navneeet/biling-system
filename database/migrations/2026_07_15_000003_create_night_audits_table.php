<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('night_audits', function (Blueprint $table) {
            $table->id();
            $table->date('audit_date');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->unique(['audit_date', 'hotel_id']);
            $table->integer('total_rooms_occupied')->default(0);
            $table->integer('total_rooms_available')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('total_payments_received', 12, 2)->default(0);
            $table->decimal('total_outstanding', 12, 2)->default(0);
            $table->decimal('room_charges_posted', 12, 2)->default(0);
            $table->decimal('tax_charges_posted', 12, 2)->default(0);
            $table->integer('complimentary_rooms')->default(0);
            $table->integer('no_show_count')->default(0);
            $table->integer('cancellation_count')->default(0);
            $table->integer('walk_in_count')->default(0);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('audited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('night_audits');
    }
};
