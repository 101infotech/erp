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
        Schema::table('hrm_attendance_days', function (Blueprint $table) {
            // Drop the broken unique key (only has employee_id)
            $table->dropUnique('hrm_attendance_days_employee_id_date_unique');

            // Add the correct composite unique key
            $table->unique(['employee_id', 'date'], 'hrm_attendance_days_employee_id_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_attendance_days', function (Blueprint $table) {
            $table->dropUnique('hrm_attendance_days_employee_id_date_unique');
            $table->unique('employee_id', 'hrm_attendance_days_employee_id_date_unique');
        });
    }
};
