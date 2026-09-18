<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_grn', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_order_id')->nullable()->constrained('inventory_purchase_orders')->onDelete('set null');
            $table->foreignId('supplier_id')->constrained('inventory_suppliers')->onDelete('cascade');
            $table->string('grn_number');
            $table->string('slug')->unique();
            $table->date('grn_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('grn_status', ['pending', 'inspected', 'accepted', 'rejected', 'partial'])->default('pending');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'grn_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_grn');
    }
};
