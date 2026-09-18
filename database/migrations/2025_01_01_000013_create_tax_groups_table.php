<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tax_group_tax', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('tax_id')->constrained()->onDelete('cascade');
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->unique(['tax_group_id', 'tax_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_group_tax');
        Schema::dropIfExists('tax_groups');
    }
};
