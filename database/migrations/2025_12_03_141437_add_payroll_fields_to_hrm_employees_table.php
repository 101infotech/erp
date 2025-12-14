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
        Schema::table('hrm_employees', function (Blueprint $table) {
            // Salary Information
            $table->decimal('basic_salary_npr', 10, 2)->nullable()->after('status');
            $table->enum('salary_type', ['monthly', 'daily', 'hourly'])->default('monthly')->after('basic_salary_npr');
            $table->decimal('hourly_rate_npr', 10, 2)->nullable()->after('salary_type');
            $table->json('allowances')->nullable()->after('hourly_rate_npr')->comment('Housing, transport, meal allowances');
            $table->json('deductions')->nullable()->after('allowances')->comment('Loans, advances');

            // Contract Information
            $table->enum('contract_type', ['permanent', 'contract', 'probation', 'intern'])->default('permanent')->after('deductions');
            $table->date('contract_start_date')->nullable()->after('contract_type');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->string('contract_document')->nullable()->after('contract_end_date');
            $table->date('probation_end_date')->nullable()->after('contract_document');
            $table->enum('employment_status', ['full-time', 'part-time'])->default('full-time')->after('probation_end_date');

            // Leave Balances
            $table->decimal('paid_leave_annual', 4, 1)->default(0)->after('employment_status');
            $table->decimal('paid_leave_sick', 4, 1)->default(0)->after('paid_leave_annual');
            $table->decimal('paid_leave_casual', 4, 1)->default(0)->after('paid_leave_sick');

            // Tax & Banking
            $table->string('pan_number')->nullable()->after('paid_leave_casual');
            $table->string('bank_account_number')->nullable()->after('pan_number');
            $table->string('bank_name')->nullable()->after('bank_account_number');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('citizenship_number')->nullable()->after('bank_branch');
            $table->text('permanent_address')->nullable()->after('citizenship_number');
            $table->text('temporary_address')->nullable()->after('permanent_address');
            $table->string('citizenship_document')->nullable()->after('temporary_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_employees', function (Blueprint $table) {
            $table->dropColumn([
                'basic_salary_npr',
                'salary_type',
                'hourly_rate_npr',
                'allowances',
                'deductions',
                'contract_type',
                'contract_start_date',
                'contract_end_date',
                'contract_document',
                'probation_end_date',
                'employment_status',
                'paid_leave_annual',
                'paid_leave_sick',
                'paid_leave_casual',
                'pan_number',
                'bank_account_number',
                'bank_name',
                'bank_branch',
                'citizenship_number',
                'permanent_address',
                'temporary_address',
                'citizenship_document'
            ]);
        });
    }
};
