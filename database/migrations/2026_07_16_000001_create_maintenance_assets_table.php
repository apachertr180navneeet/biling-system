<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category'); // e.g. Electrical, Plumbing, HVAC, Furniture, Kitchen, etc.
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->date('warranty_expiry_date')->nullable();
            $table->string('location')->nullable(); // e.g. Room 101, Lobby, Kitchen
            $table->enum('status', ['active', 'inactive', 'under_maintenance', 'disposed'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_assets');
    }
};
