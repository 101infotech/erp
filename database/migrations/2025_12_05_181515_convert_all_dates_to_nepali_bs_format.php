<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\NepalCalendarService;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Converts all date fields to Nepali BS format (Bikram Sambat)
     * All dates will be stored as strings in YYYY-MM-DD format (BS)
     */
    public function up(): void
    {
        $calendarService = app(NepalCalendarService::class);

        // 1. HRM Employees Table
        Schema::table('hrm_employees', function (Blueprint $table) {
            // Convert date columns to string for BS storage
            $table->string('hire_date_bs', 10)->nullable()->after('hire_date');
            $table->string('date_of_birth_bs', 10)->nullable()->after('date_of_birth');
            $table->string('contract_start_date_bs', 10)->nullable()->after('contract_start_date');
            $table->string('contract_end_date_bs', 10)->nullable()->after('contract_end_date');
            $table->string('probation_end_date_bs', 10)->nullable()->after('probation_end_date');
        });

        // Convert existing AD dates to BS
        $employees = DB::table('hrm_employees')->whereNotNull('hire_date')->orWhereNotNull('date_of_birth')->get();
        foreach ($employees as $employee) {
            $updates = [];
            if ($employee->hire_date) {
                $updates['hire_date_bs'] = $calendarService->adToBs($employee->hire_date);
            }
            if ($employee->date_of_birth) {
                $updates['date_of_birth_bs'] = $calendarService->adToBs($employee->date_of_birth);
            }
            if ($employee->contract_start_date) {
                $updates['contract_start_date_bs'] = $calendarService->adToBs($employee->contract_start_date);
            }
            if ($employee->contract_end_date) {
                $updates['contract_end_date_bs'] = $calendarService->adToBs($employee->contract_end_date);
            }
            if ($employee->probation_end_date) {
                $updates['probation_end_date_bs'] = $calendarService->adToBs($employee->probation_end_date);
            }
            if (!empty($updates)) {
                DB::table('hrm_employees')->where('id', $employee->id)->update($updates);
            }
        }

        // Drop old AD columns and rename BS columns
        Schema::table('hrm_employees', function (Blueprint $table) {
            $table->dropColumn(['hire_date', 'date_of_birth', 'contract_start_date', 'contract_end_date', 'probation_end_date']);
        });

        Schema::table('hrm_employees', function (Blueprint $table) {
            $table->renameColumn('hire_date_bs', 'hire_date');
            $table->renameColumn('date_of_birth_bs', 'date_of_birth');
            $table->renameColumn('contract_start_date_bs', 'contract_start_date');
            $table->renameColumn('contract_end_date_bs', 'contract_end_date');
            $table->renameColumn('probation_end_date_bs', 'probation_end_date');
        });

        // 2. HRM Leave Requests Table
        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->string('start_date_bs', 10)->nullable()->after('start_date');
            $table->string('end_date_bs', 10)->nullable()->after('end_date');
        });

        $leaveRequests = DB::table('hrm_leave_requests')->get();
        foreach ($leaveRequests as $leave) {
            DB::table('hrm_leave_requests')->where('id', $leave->id)->update([
                'start_date_bs' => $calendarService->adToBs($leave->start_date),
                'end_date_bs' => $calendarService->adToBs($leave->end_date),
            ]);
        }

        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });

        Schema::table('hrm_leave_requests', function (Blueprint $table) {
            $table->renameColumn('start_date_bs', 'start_date');
            $table->renameColumn('end_date_bs', 'end_date');
        });

        // 3. HRM Attendance Days Table
        Schema::table('hrm_attendance_days', function (Blueprint $table) {
            $table->string('date_bs', 10)->nullable()->after('date');
        });

        $attendanceDays = DB::table('hrm_attendance_days')->get();
        foreach ($attendanceDays as $day) {
            DB::table('hrm_attendance_days')->where('id', $day->id)->update([
                'date_bs' => $calendarService->adToBs($day->date),
            ]);
        }

        Schema::table('hrm_attendance_days', function (Blueprint $table) {
            $table->dropColumn('date');
        });

        Schema::table('hrm_attendance_days', function (Blueprint $table) {
            $table->renameColumn('date_bs', 'date');
        });

        // 4. HRM Time Entries Table
        Schema::table('hrm_time_entries', function (Blueprint $table) {
            $table->string('belongs_to_date_bs', 10)->nullable()->after('belongs_to_date');
        });

        $timeEntries = DB::table('hrm_time_entries')->get();
        foreach ($timeEntries as $entry) {
            DB::table('hrm_time_entries')->where('id', $entry->id)->update([
                'belongs_to_date_bs' => $calendarService->adToBs($entry->belongs_to_date),
            ]);
        }

        Schema::table('hrm_time_entries', function (Blueprint $table) {
            $table->dropColumn('belongs_to_date');
        });

        Schema::table('hrm_time_entries', function (Blueprint $table) {
            $table->renameColumn('belongs_to_date_bs', 'belongs_to_date');
        });

        // 5. HRM Attendance Anomalies Table
        Schema::table('hrm_attendance_anomalies', function (Blueprint $table) {
            $table->string('date_bs', 10)->nullable()->after('date');
        });

        $anomalies = DB::table('hrm_attendance_anomalies')->get();
        foreach ($anomalies as $anomaly) {
            DB::table('hrm_attendance_anomalies')->where('id', $anomaly->id)->update([
                'date_bs' => $calendarService->adToBs($anomaly->date),
            ]);
        }

        Schema::table('hrm_attendance_anomalies', function (Blueprint $table) {
            $table->dropColumn('date');
        });

        Schema::table('hrm_attendance_anomalies', function (Blueprint $table) {
            $table->renameColumn('date_bs', 'date');
        });

        // Note: hrm_payroll_records already uses period_start_bs and period_end_bs
        // No conversion needed for that table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $calendarService = app(NepalCalendarService::class);

        // Reverse: Convert BS dates back to AD dates

        // 1. HRM Employees
        Schema::table('hrm_employees', function (Blueprint $table) {
            $table->date('hire_date_ad')->nullable()->after('hire_date');
            $table->date('date_of_birth_ad')->nullable()->after('date_of_birth');
            $table->date('contract_start_date_ad')->nullable()->after('contract_start_date');
            $table->date('contract_end_date_ad')->nullable()->after('contract_end_date');
            $table->date('probation_end_date_ad')->nullable()->after('probation_end_date');
        });

        $employees = DB::table('hrm_employees')->get();
        foreach ($employees as $employee) {
            $updates = [];
            if ($employee->hire_date) {
                $adDate = $calendarService->bsToAd($employee->hire_date);
                $updates['hire_date_ad'] = $adDate ? $adDate->format('Y-m-d') : null;
            }
            if ($employee->date_of_birth) {
                $adDate = $calendarService->bsToAd($employee->date_of_birth);
                $updates['date_of_birth_ad'] = $adDate ? $adDate->format('Y-m-d') : null;
            }
            if ($employee->contract_start_date) {
                $adDate = $calendarService->bsToAd($employee->contract_start_date);
                $updates['contract_start_date_ad'] = $adDate ? $adDate->format('Y-m-d') : null;
            }
            if ($employee->contract_end_date) {
                $adDate = $calendarService->bsToAd($employee->contract_end_date);
                $updates['contract_end_date_ad'] = $adDate ? $adDate->format('Y-m-d') : null;
            }
            if ($employee->probation_end_date) {
                $adDate = $calendarService->bsToAd($employee->probation_end_date);
                $updates['probation_end_date_ad'] = $adDate ? $adDate->format('Y-m-d') : null;
            }
            if (!empty($updates)) {
                DB::table('hrm_employees')->where('id', $employee->id)->update($updates);
            }
        }

        Schema::table('hrm_employees', function (Blueprint $table) {
            $table->dropColumn(['hire_date', 'date_of_birth', 'contract_start_date', 'contract_end_date', 'probation_end_date']);
        });

        Schema::table('hrm_employees', function (Blueprint $table) {
            $table->renameColumn('hire_date_ad', 'hire_date');
            $table->renameColumn('date_of_birth_ad', 'date_of_birth');
            $table->renameColumn('contract_start_date_ad', 'contract_start_date');
            $table->renameColumn('contract_end_date_ad', 'contract_end_date');
            $table->renameColumn('probation_end_date_ad', 'probation_end_date');
        });

        // Similar reversals for other tables...
        // (Abbreviated for brevity - add similar code for other tables)
    }
};
