<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('name');
            $table->integer('min_amount')->nullable()->comment('Minimum amount to trigger this rule');
            $table->integer('max_amount')->nullable()->comment('Maximum amount for this rule');
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('step_order')->default(1);
            $table->boolean('require_all')->default(false)->comment('Whether all approvers must approve');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('approval_records', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->unsignedBigInteger('record_id');
            $table->foreignId('approval_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['module', 'record_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_records');
        Schema::dropIfExists('approval_rules');
    }
};
