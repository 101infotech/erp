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
        Schema::create('hrm_payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();

            // Period (Bikram Sambat only)
            $table->string('period_start_bs', 20)->comment('Format: 2081-08-15');
            $table->string('period_end_bs', 20)->comment('Format: 2081-09-14');

            // Attendance Data
            $table->decimal('total_hours_worked', 8, 2)->default(0);
            $table->integer('total_days_worked')->default(0);
            $table->decimal('overtime_hours', 6, 2)->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('unpaid_leave_days')->default(0);

            // Salary Components
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('overtime_payment', 10, 2)->default(0)->comment('Manually added');
            $table->json('allowances')->nullable()->comment('Individual allowance breakdown');
            $table->decimal('allowances_total', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2);

            // Deductions
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->boolean('tax_overridden')->default(false);
            $table->text('tax_override_reason')->nullable();
            $table->json('deductions')->nullable()->comment('Individual deduction breakdown');
            $table->decimal('deductions_total', 10, 2)->default(0);
            $table->decimal('unpaid_leave_deduction', 10, 2)->default(0);

            // Final Amount
            $table->decimal('net_salary', 10, 2);

            // Status & Approval
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('payment_method', ['bank_transfer', 'cash', 'cheque'])->nullable();
            $table->string('transaction_reference')->nullable();

            // Anomalies
            $table->json('anomalies')->nullable()->comment('Flagged attendance issues');
            $table->boolean('anomalies_reviewed')->default(false);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'period_start_bs', 'period_end_bs'], 'payroll_empid_period_bs_unique');
            $table->index(['period_start_bs', 'period_end_bs']);
            $table->index(['status', 'period_start_bs']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hrm_payroll_records');
    }
};
