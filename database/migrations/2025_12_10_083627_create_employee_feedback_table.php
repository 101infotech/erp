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
        Schema::create('employee_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('feelings')->comment('How the employee is feeling');
            $table->text('work_progress')->comment('Progress made this week');
            $table->text('self_improvements')->comment('Areas for self-improvement');
            $table->text('admin_notes')->nullable()->comment('Admin feedback/notes');
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Index for weekly submission tracking
            $table->index(['user_id', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_feedback');
    }
};
