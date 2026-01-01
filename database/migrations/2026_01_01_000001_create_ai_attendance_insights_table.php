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
        Schema::create('ai_attendance_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->date('analysis_date');

            // Attendance patterns
            $table->decimal('avg_clock_in_time', 5, 2)->nullable()->comment('Average clock-in hour (24h format)');
            $table->decimal('clock_in_consistency_score', 5, 2)->default(0)->comment('0-100 score');
            $table->integer('late_arrivals_count')->default(0);
            $table->integer('early_departures_count')->default(0);
            $table->decimal('avg_daily_hours', 5, 2)->nullable();
            $table->integer('absent_days_count')->default(0);

            // Weekly patterns
            $table->json('weekly_pattern')->nullable()->comment('Clock-in patterns by day of week');
            $table->string('most_productive_day')->nullable();
            $table->string('least_productive_day')->nullable();

            // AI Generated Insights
            $table->text('ai_summary')->nullable()->comment('AI-generated summary of attendance behavior');
            $table->text('ai_suggestions')->nullable()->comment('AI suggestions for improvement');
            $table->json('ai_metadata')->nullable()->comment('Additional AI analysis data');

            // Scores
            $table->decimal('punctuality_score', 5, 2)->default(0)->comment('0-100 score');
            $table->decimal('regularity_score', 5, 2)->default(0)->comment('0-100 score');
            $table->decimal('overall_score', 5, 2)->default(0)->comment('0-100 overall attendance score');

            // Trend
            $table->string('trend')->default('stable')->comment('improving, declining, stable');
            $table->decimal('trend_change', 5, 2)->default(0)->comment('Percentage change from previous period');

            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'analysis_date']);
            $table->index('analysis_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_attendance_insights');
    }
};
