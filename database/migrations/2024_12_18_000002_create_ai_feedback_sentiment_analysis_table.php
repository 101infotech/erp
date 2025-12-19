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
        Schema::create('ai_feedback_sentiment_analysis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('employee_feedback')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Sentiment scores (0-1, where 1 is most positive)
            $table->decimal('overall_sentiment', 3, 2)->nullable(); // Overall sentiment (0-1)
            $table->decimal('feelings_sentiment', 3, 2)->nullable(); // Sentiment of 'feelings' field
            $table->decimal('progress_sentiment', 3, 2)->nullable(); // Sentiment of 'work_progress' field
            $table->decimal('improvement_sentiment', 3, 2)->nullable(); // Sentiment of 'self_improvements' field

            // Sentiment classification
            $table->enum('overall_classification', ['very_negative', 'negative', 'neutral', 'positive', 'very_positive'])->nullable();

            // Trending analysis
            $table->decimal('trend_change', 3, 2)->nullable(); // Change from previous week (-1 to +1)
            $table->enum('trend_direction', ['declining', 'stable', 'improving'])->nullable();

            // AI metadata
            $table->json('ai_response')->nullable(); // Full AI response
            $table->string('ai_model')->nullable(); // Which AI model was used
            $table->integer('processing_time_ms')->nullable(); // How long it took to process

            // Flags for alerts
            $table->boolean('needs_manager_attention')->default(false);
            $table->text('alert_reason')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['overall_classification', 'created_at']);
            $table->index(['needs_manager_attention', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feedback_sentiment_analysis');
    }
};
