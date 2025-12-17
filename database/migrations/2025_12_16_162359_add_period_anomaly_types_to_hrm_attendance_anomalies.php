<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the anomaly_type enum to include new period-level anomaly types
        DB::statement("ALTER TABLE hrm_attendance_anomalies MODIFY COLUMN anomaly_type ENUM(
            'missing_clock_out',
            'excessive_hours',
            'weekend_work_no_ot',
            'location_inconsistency',
            'duplicate_entry',
            'negative_time',
            'excessive_absences',
            'high_absences',
            'low_work_hours',
            'consecutive_absences',
            'frequent_late_arrivals'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE hrm_attendance_anomalies MODIFY COLUMN anomaly_type ENUM(
            'missing_clock_out',
            'excessive_hours',
            'weekend_work_no_ot',
            'location_inconsistency',
            'duplicate_entry',
            'negative_time'
        )");
    }
};
