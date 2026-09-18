<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained()->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('photo_path')->nullable();
            $table->string('id_type')->nullable()->comment('passport, drivers_license, national_id');
            $table->string('id_number')->nullable();
            $table->date('id_expiry_date')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('address_proof_type')->nullable();
            $table->string('address_proof_path')->nullable();
            $table->string('occupation')->nullable();
            $table->string('dietary_preference')->nullable();
            $table->string('room_preference')->nullable();
            $table->string('bed_preference')->nullable();
            $table->string('pillow_preference')->nullable();
            $table->string('arrival_preference')->nullable();
            $table->string('communication_preference')->nullable();
            $table->text('special_notes')->nullable();
            $table->boolean('vip_status')->default(false);
            $table->string('vip_level')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_profiles');
    }
};
