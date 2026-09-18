<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->datetime('check_out_time')->nullable();
            $table->decimal('final_bill_amount', 12, 2)->default(0);
            $table->decimal('total_charges', 12, 2)->default(0);
            $table->decimal('total_payments', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2)->default(0);
            $table->string('room_condition')->nullable()->comment('good, damaged');
            $table->text('damage_notes')->nullable();
            $table->decimal('damage_charges', 12, 2)->default(0);
            $table->tinyInteger('feedback_rating')->nullable()->comment('1-5');
            $table->text('feedback_notes')->nullable();
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_outs');
    }
};
