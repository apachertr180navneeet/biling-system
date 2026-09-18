<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->string('adjustment_number');
            $table->string('slug')->unique();
            $table->date('adjustment_date');
            $table->enum('adjustment_type', ['addition', 'subtraction', 'damage', 'expired', 'theft', 'correction']);
            $table->integer('quantity_before')->default(0);
            $table->integer('adjustment_quantity');
            $table->integer('quantity_after')->default(0);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total_value', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'adjustment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_adjustments');
    }
};
