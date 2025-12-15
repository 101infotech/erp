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
            // Excel-compatible fields for transparency
            $table->decimal('per_day_rate', 10, 2)->default(0)->after('basic_salary')
                ->comment('Per day rate (basic_salary / month_total_days)');
            $table->integer('total_payable_days')->default(0)->after('per_day_rate')
                ->comment('Days to pay (days_worked + paid_leave_days)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $table->dropColumn([
                'per_day_rate',
                'total_payable_days',
            ]);
        });
    }
};
