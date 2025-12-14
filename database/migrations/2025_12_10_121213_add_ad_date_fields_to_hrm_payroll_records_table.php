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
            $table->date('period_start_ad')->nullable()->after('period_end_bs');
            $table->date('period_end_ad')->nullable()->after('period_start_ad');

            // Add indexes for better query performance
            $table->index(['employee_id', 'period_start_ad', 'period_end_ad'], 'idx_employee_period_ad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $table->dropIndex('idx_employee_period_ad');
            $table->dropColumn(['period_start_ad', 'period_end_ad']);
        });
    }
};
