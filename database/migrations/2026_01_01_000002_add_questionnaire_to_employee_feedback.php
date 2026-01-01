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
        Schema::table('employee_feedback', function (Blueprint $table) {
            // Add new questionnaire fields
            $table->integer('stress_level')->nullable()->comment('1-5 scale');
            $table->integer('workload_level')->nullable()->comment('1-5 scale');
            $table->integer('work_satisfaction')->nullable()->comment('1-5 scale');
            $table->integer('team_collaboration')->nullable()->comment('1-5 scale');
            $table->integer('mental_wellbeing')->nullable()->comment('1-5 scale');
            $table->text('challenges_faced')->nullable()->comment('Challenges this week');
            $table->text('achievements')->nullable()->comment('What went well');
            $table->text('support_needed')->nullable()->comment('Support needed from management');
            $table->text('complaints')->nullable()->comment('Any complaints or concerns');

            // Keep old fields for backward compatibility but make nullable
            $table->text('feelings')->nullable()->change();
            $table->text('work_progress')->nullable()->change();
            $table->text('self_improvements')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_feedback', function (Blueprint $table) {
            $table->dropColumn([
                'stress_level',
                'workload_level',
                'work_satisfaction',
                'team_collaboration',
                'mental_wellbeing',
                'challenges_faced',
                'achievements',
                'support_needed',
                'complaints',
            ]);
        });
    }
};
