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
            // Rename and add personal information fields
            if (Schema::hasColumn('hrm_employees', 'full_name') && !Schema::hasColumn('hrm_employees', 'name')) {
                $table->renameColumn('full_name', 'name');
            }
            if (Schema::hasColumn('hrm_employees', 'birth_date') && !Schema::hasColumn('hrm_employees', 'date_of_birth')) {
                $table->renameColumn('birth_date', 'date_of_birth');
            }
            if (Schema::hasColumn('hrm_employees', 'join_date') && !Schema::hasColumn('hrm_employees', 'hire_date')) {
                $table->renameColumn('join_date', 'hire_date');
            }

            // Personal details
            if (!Schema::hasColumn('hrm_employees', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('hrm_employees', 'blood_group')) {
                $table->string('blood_group', 10)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('hrm_employees', 'marital_status')) {
                $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('blood_group');
            }

            // Emergency contact (update existing fields)
            if (Schema::hasColumn('hrm_employees', 'emergency_contact') && !Schema::hasColumn('hrm_employees', 'emergency_contact_name')) {
                $table->renameColumn('emergency_contact', 'emergency_contact_name');
            }
            if (Schema::hasColumn('hrm_employees', 'emergency_phone') && !Schema::hasColumn('hrm_employees', 'emergency_contact_phone')) {
                $table->renameColumn('emergency_phone', 'emergency_contact_phone');
            }
            if (!Schema::hasColumn('hrm_employees', 'emergency_contact_relationship')) {
                $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            }

            // Employment details
            if (!Schema::hasColumn('hrm_employees', 'employment_type')) {
                $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'intern'])->default('full-time')->after('employment_status');
            }
            if (!Schema::hasColumn('hrm_employees', 'working_days_per_week')) {
                $table->integer('working_days_per_week')->default(5)->after('employment_type');
            }

            // Contract details (additional)
            if (!Schema::hasColumn('hrm_employees', 'probation_period_months')) {
                $table->integer('probation_period_months')->nullable()->after('probation_end_date');
            }

            // Salary details (additional)
            if (!Schema::hasColumn('hrm_employees', 'salary_amount')) {
                $table->decimal('salary_amount', 10, 2)->nullable()->after('basic_salary_npr')->comment('Alias for basic_salary_npr');
            }
            if (!Schema::hasColumn('hrm_employees', 'allowances_amount')) {
                $table->decimal('allowances_amount', 10, 2)->nullable()->after('allowances')->comment('Total allowances amount');
            }

            // Tax information
            if (!Schema::hasColumn('hrm_employees', 'tax_regime')) {
                $table->string('tax_regime')->nullable()->after('pan_number')->comment('Old or New tax regime');
            }

            // Avatar/profile
            if (!Schema::hasColumn('hrm_employees', 'avatar_url')) {
                $table->string('avatar_url')->nullable()->after('avatar');
            }

            // Additional leave balances
            if (!Schema::hasColumn('hrm_employees', 'sick_leave_balance')) {
                $table->decimal('sick_leave_balance', 4, 1)->default(0)->after('paid_leave_sick');
            }
            if (!Schema::hasColumn('hrm_employees', 'casual_leave_balance')) {
                $table->decimal('casual_leave_balance', 4, 1)->default(0)->after('paid_leave_casual');
            }
            if (!Schema::hasColumn('hrm_employees', 'annual_leave_balance')) {
                $table->decimal('annual_leave_balance', 4, 1)->default(0)->after('paid_leave_annual');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_employees', function (Blueprint $table) {
            // Rename back
            if (Schema::hasColumn('hrm_employees', 'name')) {
                $table->renameColumn('name', 'full_name');
            }
            if (Schema::hasColumn('hrm_employees', 'date_of_birth')) {
                $table->renameColumn('date_of_birth', 'birth_date');
            }
            if (Schema::hasColumn('hrm_employees', 'hire_date')) {
                $table->renameColumn('hire_date', 'join_date');
            }
            if (Schema::hasColumn('hrm_employees', 'emergency_contact_name')) {
                $table->renameColumn('emergency_contact_name', 'emergency_contact');
            }
            if (Schema::hasColumn('hrm_employees', 'emergency_contact_phone')) {
                $table->renameColumn('emergency_contact_phone', 'emergency_phone');
            }

            // Drop added columns
            $columns = [
                'gender',
                'blood_group',
                'marital_status',
                'emergency_contact_relationship',
                'employment_type',
                'working_days_per_week',
                'probation_period_months',
                'salary_amount',
                'allowances_amount',
                'tax_regime',
                'avatar_url',
                'sick_leave_balance',
                'casual_leave_balance',
                'annual_leave_balance',
            ];
            $existing = collect($columns)->filter(fn($c) => Schema::hasColumn('hrm_employees', $c))->all();
            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};
