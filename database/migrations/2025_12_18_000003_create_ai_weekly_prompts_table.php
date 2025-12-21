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
        if (!Schema::hasTable('ai_weekly_prompts')) {
            Schema::create('ai_weekly_prompts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

                // Prompt Details
                $table->string('title');
                $table->text('prompt');
                $table->enum('category', ['wellbeing', 'productivity', 'culture', 'engagement', 'development', 'general'])->default('general');
                $table->json('follow_up_questions')->nullable(); // Follow-up questions

                // Metadata
                $table->date('prompt_date');
                $table->integer('week_number');
                $table->integer('year');

                // Response Tracking
                $table->boolean('answered')->default(false);
                $table->text('answer')->nullable();
                $table->timestamp('answered_at')->nullable();
                $table->boolean('skipped')->default(false);

                // Generation Details
                $table->string('ai_model')->default('gpt-4');
                $table->string('ai_provider')->default('openai');
                $table->json('generation_context')->nullable();
                $table->boolean('is_adaptive')->default(true);

                // Context Information
                $table->json('employee_context')->nullable(); // Previous responses, department, role
                $table->json('company_context')->nullable(); // Company news, initiatives

                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('employee_id');
                $table->index('prompt_date');
                $table->index(['year', 'week_number']);
                $table->index('category');
                $table->index('answered');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_weekly_prompts');
    }
};
