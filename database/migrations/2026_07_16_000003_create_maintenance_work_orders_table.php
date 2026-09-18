<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('work_order_number')->unique();
            $table->foreignId('asset_id')->nullable()->constrained('maintenance_assets')->onDelete('set null');
            $table->string('title');
            $table->enum('type', ['preventive', 'breakdown'])->default('breakdown');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('schedule_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_work_orders');
    }
};
