<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('capacity', 8, 0)->default(0);
            $table->decimal('area_sqft', 10, 2)->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->string('price_unit')->default('per_event');
            $table->string('floor')->nullable();
            $table->boolean('is_ac')->default(false);
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_stage')->default(false);
            $table->boolean('has_sound_system')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->string('image_path')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('halls');
    }
};
