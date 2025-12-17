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
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            // Add field for manually recording verbally approved leave days
            // These will be excluded from required hours calculation
            $table->integer('verbal_leave_days')->default(0)->after('unpaid_leave_days')
                ->comment('Manually recorded leave days that were verbally approved but not formally recorded in the system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $table->dropColumn('verbal_leave_days');
        });
    }
};
