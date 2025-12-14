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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->enum('recipient_type', ['all', 'specific'])->default('all');
            $table->json('recipient_ids')->nullable()->comment('User IDs when recipient_type is specific');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('send_email')->default(true);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
