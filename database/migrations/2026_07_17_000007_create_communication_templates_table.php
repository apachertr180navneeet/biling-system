<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('module');
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->string('subject')->nullable()->comment('Email subject');
            $table->text('body');
            $table->json('variables')->nullable()->comment('Available template variables');
            $table->text('sample_data')->nullable()->comment('JSON sample data for preview');
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['module', 'channel', 'name']);
        });

        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('communication_templates')->nullOnDelete();
            $table->string('channel');
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->enum('status', ['pending', 'sent', 'failed', 'delivered', 'read'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status']);
            $table->index(['recipient']);
        });

        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->string('event')->unique()->comment('e.g. reservation_confirmed, check_in_reminder');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('communication_templates');
    }
};
