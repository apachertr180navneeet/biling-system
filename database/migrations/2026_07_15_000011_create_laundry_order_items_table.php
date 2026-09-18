<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('laundry_item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_sent')->default(0);
            $table->integer('quantity_received')->nullable();
            $table->integer('damage_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_order_items');
    }
};
