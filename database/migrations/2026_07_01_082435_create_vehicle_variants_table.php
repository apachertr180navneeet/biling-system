<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('vehicle_models')->cascadeOnDelete();
            $table->string('name');
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->decimal('ex_showroom_price', 12, 2)->default(0);
            $table->string('hsn_code', 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['model_id', 'name']);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_variants');
    }
};
