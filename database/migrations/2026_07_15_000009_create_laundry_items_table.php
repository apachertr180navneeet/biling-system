<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('item_type', ['linen', 'towel', 'uniform', 'other'])->default('linen');
            $table->integer('quantity')->default(0);
            $table->enum('unit', ['pieces', 'kg', 'pairs'])->default('pieces');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_items');
    }
};
