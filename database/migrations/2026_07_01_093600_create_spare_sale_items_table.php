<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('spare_part_id')->nullable()->constrained()->onDelete('set null');
            $table->string('part_name');
            $table->string('hsn_code')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare_sale_items');
    }
};
