<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('gstin', 20)->nullable()->after('zipcode');
            $table->string('pan', 10)->nullable()->after('gstin');
            $table->string('tan', 10)->nullable()->after('pan');
            $table->string('state_code', 2)->nullable()->after('tan');
            $table->string('state_name', 50)->nullable()->after('state_code');
            $table->boolean('is_gst_registered')->default(false)->after('state_name');
            $table->boolean('is_einvoice_enabled')->default(false)->after('is_gst_registered');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'pan', 'tan', 'state_code', 'state_name', 'is_gst_registered', 'is_einvoice_enabled']);
        });
    }
};
