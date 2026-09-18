<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_category_id')->nullable()->constrained('vendor_categories')->onDelete('set null');
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('gstin', 30)->nullable();
            $table->string('pan', 20)->nullable();
            $table->string('cin', 30)->nullable();
            $table->string('tan', 30)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc', 20)->nullable();
            $table->string('bank_branch')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->string('payment_terms')->nullable();
            $table->enum('vendor_type', ['material', 'service', 'both'])->default('material');
            $table->enum('rating', ['excellent', 'good', 'average', 'poor'])->nullable();
            $table->date('agreement_start_date')->nullable();
            $table->date('agreement_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'vendor_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
