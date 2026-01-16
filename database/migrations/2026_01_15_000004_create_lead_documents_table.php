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
        Schema::create('lead_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->enum('document_type', ['photo', 'design', 'contract', 'quotation', 'report', 'other']);
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->string('file_size', 50);
            $table->string('mime_type', 100);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('uploaded_by_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('service_leads')->onDelete('cascade');
            $table->foreign('uploaded_by_id')->references('id')->on('users');

            $table->index(['lead_id', 'document_type']);
            $table->index('uploaded_by_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_documents');
    }
};
