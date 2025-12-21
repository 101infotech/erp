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
            // Month and working hour configuration
            if (!Schema::hasColumn('hrm_payroll_records', 'month_total_days')) {
                $table->integer('month_total_days')->nullable()->after('period_end_bs')
                    ->comment('Total days in BS month (29/30/31)');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'standard_working_hours_per_day')) {
                $table->decimal('standard_working_hours_per_day', 4, 2)->default(8.00)->after('month_total_days')
                    ->comment('Standard working hours per day');
            }

            // Working hours tracking
            if (!Schema::hasColumn('hrm_payroll_records', 'total_working_hours_required')) {
                $table->decimal('total_working_hours_required', 8, 2)->default(0)->after('total_hours_worked')
                    ->comment('Expected total hours for the period');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'total_working_hours_missing')) {
                $table->decimal('total_working_hours_missing', 8, 2)->default(0)->after('total_working_hours_required')
                    ->comment('Hours short of requirement');
            }

            // Hourly deduction (suggested by system, approved by admin)
            if (!Schema::hasColumn('hrm_payroll_records', 'hourly_deduction_suggested')) {
                $table->decimal('hourly_deduction_suggested', 10, 2)->default(0)->after('total_working_hours_missing')
                    ->comment('System calculated deduction for missing hours');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'hourly_deduction_amount')) {
                $table->decimal('hourly_deduction_amount', 10, 2)->default(0)->after('hourly_deduction_suggested')
                    ->comment('Admin approved hourly deduction amount');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'hourly_deduction_approved')) {
                $table->boolean('hourly_deduction_approved')->default(false)->after('hourly_deduction_amount')
                    ->comment('Whether admin approved the hourly deduction');
            }

            // Leave tracking
            if (!Schema::hasColumn('hrm_payroll_records', 'paid_leave_days_used')) {
                $table->integer('paid_leave_days_used')->default(0)->after('unpaid_leave_days')
                    ->comment('Paid leave days used in this period');
            }

            // Advance payment
            if (!Schema::hasColumn('hrm_payroll_records', 'advance_payment')) {
                $table->decimal('advance_payment', 10, 2)->default(0)->after('deductions_total')
                    ->comment('Advance salary taken');
            }
            if (!Schema::hasColumn('hrm_payroll_records', 'advance_payment_details')) {
                $table->json('advance_payment_details')->nullable()->after('advance_payment')
                    ->comment('Details of advance payment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_payroll_records', function (Blueprint $table) {
            $columns = [
                'month_total_days',
                'standard_working_hours_per_day',
                'total_working_hours_required',
                'total_working_hours_missing',
                'hourly_deduction_suggested',
                'hourly_deduction_amount',
                'hourly_deduction_approved',
                'paid_leave_days_used',
                'advance_payment',
                'advance_payment_details',
            ];
            $existing = collect($columns)->filter(fn($c) => Schema::hasColumn('hrm_payroll_records', $c))->all();
            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};
