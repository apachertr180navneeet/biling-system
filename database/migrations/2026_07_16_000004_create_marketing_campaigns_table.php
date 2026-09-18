<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('name');
            $table->enum('channel', ['email', 'sms', 'whatsapp']);
            $table->string('subject')->nullable(); // For email
            $table->text('content');
            $table->enum('target_audience', ['all_guests', 'loyalty_members', 'recent_guests'])->default('all_guests');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            $table->integer('total_recipients')->default(0);
            $table->integer('successful_deliveries')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
