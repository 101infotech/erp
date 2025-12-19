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
        Schema::create('ai_performance_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // Performance Metrics
            $table->date('analysis_date');
            $table->string('period_type'); // weekly, monthly, quarterly

            // Sentiment Trends
            $table->decimal('avg_sentiment_score', 3, 2)->default(0.50);
            $table->integer('positive_count')->default(0);
            $table->integer('neutral_count')->default(0);
            $table->integer('negative_count')->default(0);

            // Engagement Metrics
            $table->integer('total_feedback_count')->default(0);
            $table->decimal('engagement_score', 3, 2)->default(0.50);
            $table->decimal('response_timeliness_score', 3, 2)->default(0.50);

            // Identified Themes
            $table->json('positive_themes')->nullable(); // Recurring positive themes
            $table->json('negative_themes')->nullable(); // Recurring negative themes
            $table->json('improvement_areas')->nullable(); // AI identified areas for improvement

            // Health Indicators
            $table->enum('employee_mood', ['excellent', 'good', 'neutral', 'concerning', 'critical'])->default('neutral');
            $table->boolean('burnout_risk')->default(false);
            $table->boolean('retention_risk')->default(false);
            $table->decimal('retention_probability', 3, 2)->default(0.85);

            // AI Recommendations
            $table->json('hr_recommendations')->nullable(); // Recommendations for HR
            $table->json('manager_recommendations')->nullable(); // Recommendations for manager
            $table->json('employee_recommendations')->nullable(); // Suggestions for employee

            // Comparison
            $table->decimal('department_sentiment_comparison', 3, 2)->nullable();
            $table->decimal('company_sentiment_comparison', 3, 2)->nullable();
            $table->integer('percentile_rank')->nullable(); // Where employee ranks

            // Analysis Details
            $table->string('ai_model')->default('gpt-4');
            $table->string('ai_provider')->default('openai');
            $table->json('analysis_parameters')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('employee_id');
            $table->index('analysis_date');
            $table->index('period_type');
            $table->index('employee_mood');
            $table->index(['employee_id', 'analysis_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_performance_insights');
    }
};
