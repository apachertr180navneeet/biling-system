<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gst_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('return_number', 50)->unique();
            $table->string('period'); // e.g., "2026-07"
            $table->enum('return_type', ['gstr1', 'gstr3b', 'gstr9'])->default('gstr1');
            $table->date('filing_date')->nullable();
            $table->decimal('total_taxable_value', 15, 2)->default(0);
            $table->decimal('total_cgst', 15, 2)->default(0);
            $table->decimal('total_sgst', 15, 2)->default(0);
            $table->decimal('total_igst', 15, 2)->default(0);
            $table->decimal('total_cess', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->enum('status', ['draft', 'filed', 'amended'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gst_returns');
    }
};
