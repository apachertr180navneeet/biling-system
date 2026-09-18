<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->string('document_name');
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'expired', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vendor_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
