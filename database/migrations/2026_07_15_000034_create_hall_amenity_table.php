<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_amenity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained()->onDelete('cascade');
            $table->foreignId('hall_amenity_id')->constrained('hall_amenities')->onDelete('cascade');
            $table->decimal('additional_cost', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['hall_id', 'hall_amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_amenity');
    }
};
