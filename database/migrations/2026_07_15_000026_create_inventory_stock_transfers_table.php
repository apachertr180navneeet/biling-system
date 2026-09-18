<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('transfer_number');
            $table->string('slug')->unique();
            $table->date('transfer_date');
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->text('notes')->nullable();
            $table->enum('transfer_status', ['draft', 'pending', 'approved', 'in_transit', 'received', 'cancelled'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'transfer_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_transfers');
    }
};
