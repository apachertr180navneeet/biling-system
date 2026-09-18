<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timezone', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('offset');
            $table->string('abbreviation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timezone');
    }
};
