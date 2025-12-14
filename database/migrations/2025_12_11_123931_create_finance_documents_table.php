<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('finance_documents', function (Blueprint $table) {
            $table->id();
            $table->string('documentable_type'); // Customer, Vendor, Sale, Purchase
            $table->unsignedBigInteger('documentable_id');
            $table->foreignId('company_id')->constrained('finance_companies')->onDelete('cascade');
            $table->string('type'); // invoice, receipt, contract, agreement, pan_card, registration, other
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // pdf, image, doc
            $table->integer('file_size'); // in bytes
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_documents');
    }
};
