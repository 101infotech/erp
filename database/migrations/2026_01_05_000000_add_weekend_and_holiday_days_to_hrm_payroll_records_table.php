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
            if (!Schema::hasColumn('hrm_payroll_records', 'weekend_days')) {
                $table->integer('weekend_days')->default(0)->after('total_payable_days')
                    ->comment('Number of weekends (Saturdays) in the payroll period');
            }

            if (!Schema::hasColumn('hrm_payroll_records', 'holiday_days')) {
                $table->integer('holiday_days')->default(0)->after('weekend_days')
                    ->comment('Number of active company-wide holidays in the payroll period');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $columns = ['weekend_days', 'holiday_days'];
            $existing = collect($columns)->filter(fn($c) => Schema::hasColumn('hrm_payroll_records', $c))->all();

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};
