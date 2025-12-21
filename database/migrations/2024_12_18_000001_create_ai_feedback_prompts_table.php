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
        if (!Schema::hasTable('ai_feedback_prompts')) {
            Schema::create('ai_feedback_prompts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('feedback_id')->nullable()->constrained('employee_feedback')->nullOnDelete();
                $table->text('prompt'); // The AI-generated question
                $table->json('context')->nullable(); // Context used to generate the prompt (department, history, etc.)
                $table->string('category')->default('general'); // e.g., 'feelings', 'work_progress', 'improvements'
                $table->integer('sequence_number')->default(1); // Order in which prompt was presented
                $table->json('ai_metadata')->nullable(); // Metadata from AI API response
                $table->boolean('is_used')->default(false); // Whether this prompt was used in actual feedback
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['is_used', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feedback_prompts');
    }
};
