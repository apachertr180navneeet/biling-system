<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['vehicle_stock_id']);
            $table->dropColumn('vehicle_stock_id');
            $table->text('vehicle_description')->nullable()->after('invoice_type');
            $table->string('chassis_number')->nullable()->after('vehicle_description');
            $table->string('engine_number')->nullable()->after('chassis_number');
            $table->year('mfg_year')->nullable()->after('engine_number');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['vehicle_stock_id']);
            $table->dropColumn('vehicle_stock_id');
            $table->text('vehicle_description')->nullable()->after('sale_number');
        });

        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropForeign(['vehicle_stock_id']);
            $table->dropColumn('vehicle_stock_id');
        });

        Schema::dropIfExists('vehicle_stocks');
        Schema::dropIfExists('spare_part_stocks');
    }

    public function down(): void
    {
        Schema::create('vehicle_stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('qty')->default(1);
            $table->string('chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->foreignId('color_id')->constrained('vehicle_colors')->cascadeOnDelete();
            $table->year('mfg_year')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->string('status')->default('available');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('spare_part_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(5);
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['vehicle_description', 'chassis_number', 'engine_number', 'mfg_year']);
            $table->foreignId('vehicle_stock_id')->nullable()->constrained('vehicle_stocks')->nullOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('vehicle_description');
            $table->foreignId('vehicle_stock_id')->nullable()->constrained('vehicle_stocks')->nullOnDelete();
        });

        Schema::table('job_cards', function (Blueprint $table) {
            $table->foreignId('vehicle_stock_id')->nullable()->constrained('vehicle_stocks')->nullOnDelete();
        });
    }
};
