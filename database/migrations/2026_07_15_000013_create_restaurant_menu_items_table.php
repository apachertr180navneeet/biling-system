<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('main_course');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->integer('preparation_time')->nullable()->comment('in minutes');
            $table->boolean('is_available')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_menu_items');
    }
};
