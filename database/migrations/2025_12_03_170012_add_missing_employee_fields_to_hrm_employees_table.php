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
            $table->renameColumn('full_name', 'name');
            $table->renameColumn('birth_date', 'date_of_birth');
            $table->renameColumn('join_date', 'hire_date');

            // Personal details
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('blood_group', 10)->nullable()->after('gender');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('blood_group');

            // Emergency contact (update existing fields)
            $table->renameColumn('emergency_contact', 'emergency_contact_name');
            $table->renameColumn('emergency_phone', 'emergency_contact_phone');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');

            // Employment details
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'intern'])->default('full-time')->after('employment_status');
            $table->integer('working_days_per_week')->default(5)->after('employment_type');

            // Contract details (additional)
            $table->integer('probation_period_months')->nullable()->after('probation_end_date');

            // Salary details (additional)
            $table->decimal('salary_amount', 10, 2)->nullable()->after('basic_salary_npr')->comment('Alias for basic_salary_npr');
            $table->decimal('allowances_amount', 10, 2)->nullable()->after('allowances')->comment('Total allowances amount');

            // Tax information
            $table->string('tax_regime')->nullable()->after('pan_number')->comment('Old or New tax regime');

            // Avatar/profile
            $table->string('avatar_url')->nullable()->after('avatar');

            // Additional leave balances
            $table->decimal('sick_leave_balance', 4, 1)->default(0)->after('paid_leave_sick');
            $table->decimal('casual_leave_balance', 4, 1)->default(0)->after('paid_leave_casual');
            $table->decimal('annual_leave_balance', 4, 1)->default(0)->after('paid_leave_annual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_employees', function (Blueprint $table) {
            // Rename back
            $table->renameColumn('name', 'full_name');
            $table->renameColumn('date_of_birth', 'birth_date');
            $table->renameColumn('hire_date', 'join_date');
            $table->renameColumn('emergency_contact_name', 'emergency_contact');
            $table->renameColumn('emergency_contact_phone', 'emergency_phone');

            // Drop added columns
            $table->dropColumn([
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
            ]);
        });
    }
};
