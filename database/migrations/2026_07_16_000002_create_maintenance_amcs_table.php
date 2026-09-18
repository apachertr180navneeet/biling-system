<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_amcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('maintenance_assets')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->string('contract_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('cost', 10, 2);
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_amcs');
    }
};
