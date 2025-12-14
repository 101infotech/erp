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
        Schema::create('hrm_attendance_anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->foreignId('attendance_day_id')->nullable()->constrained('hrm_attendance_days')->cascadeOnDelete();
            $table->date('date');
            $table->enum('anomaly_type', [
                'missing_clock_out',
                'excessive_hours',
                'weekend_work_no_ot',
                'location_inconsistency',
                'duplicate_entry',
                'negative_time'
            ])->index();
            $table->text('description');
            $table->json('metadata')->nullable()->comment('Additional context data');
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('reviewed')->default(false)->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'reviewed']);
            $table->index(['date', 'anomaly_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_attendance_anomalies');
    }
};
