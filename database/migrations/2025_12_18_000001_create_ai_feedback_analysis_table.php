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
        if (!Schema::hasTable('ai_feedback_analysis')) {
            Schema::create('ai_feedback_analysis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('feedback_id')->constrained('employee_feedback')->onDelete('cascade');
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

                // Sentiment Analysis
                $table->enum('sentiment', ['positive', 'neutral', 'negative'])->default('neutral');
                $table->decimal('sentiment_score', 3, 2); // 0.00 to 1.00
                $table->json('sentiment_details')->nullable(); // Additional sentiment data

                // AI Generated Content
                $table->text('ai_generated_questions')->nullable(); // JSON array of questions
                $table->json('question_context')->nullable(); // Context for questions
                $table->json('tags')->nullable(); // AI-identified tags/themes

                // Analysis Metadata
                $table->string('ai_model')->default('gpt-4'); // Which AI model was used
                $table->string('ai_provider')->default('openai'); // Which provider was used
                $table->json('analysis_metadata')->nullable(); // Additional analysis data

                // Processing
                $table->integer('tokens_used')->default(0); // API tokens consumed
                $table->decimal('processing_time', 8, 3)->nullable(); // Time taken in seconds
                $table->enum('status', ['processed', 'failed', 'pending'])->default('processed');
                $table->text('error_message')->nullable(); // Error details if failed

                // Recommendations
                $table->json('recommendations')->nullable(); // HR recommendations based on feedback
                $table->boolean('requires_manager_attention')->default(false);
                $table->text('manager_notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('feedback_id');
                $table->index('employee_id');
                $table->index('sentiment');
                $table->index('requires_manager_attention');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feedback_analysis');
    }
};
