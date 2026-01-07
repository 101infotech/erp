<?php

namespace Database\Seeders;

use App\Models\FinanceCompany;
use App\Models\HrmAttendanceAnomaly;
use App\Models\HrmAttendanceDay;
use App\Models\HrmCompany;
use App\Models\HrmDepartment;
use App\Models\HrmEmployee;
use App\Models\HrmLeaveRequest;
use App\Models\HrmPayrollRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HrmDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $financeCompany = FinanceCompany::first();

        $hrmCompany = HrmCompany::firstOrCreate(
            ['name' => 'Saubhagya Group HR'],
            [
                'contact_email' => 'hr@saubhagyagroup.com',
                'address' => 'Kathmandu, Nepal',
                'finance_company_id' => $financeCompany?->id,
            ]
        );

        $departments = [
            'Engineering' => 'Product delivery and QA',
            'Finance & Ops' => 'Finance, compliance, and operations',
            'HR & Admin' => 'People operations and admin',
        ];

        $departmentModels = collect();
        foreach ($departments as $name => $description) {
            $departmentModels->put(
                $name,
                HrmDepartment::firstOrCreate(
                    ['company_id' => $hrmCompany->id, 'name' => $name],
                    ['description' => $description]
                )
            );
        }

        $employees = [
            [
                'name' => 'Aarav Shrestha',
                'email' => 'aarav@saubhagyagroup.com',
                'position' => 'Engineering Manager',
                'department' => 'Engineering',
                'salary' => 145000,
                'join_date' => Carbon::now()->subYears(2)->toDateString(),
                'status' => 'active',
            ],
            [
                'name' => 'Sujata Khadka',
                'email' => 'sujata@saubhagyagroup.com',
                'position' => 'Finance Lead',
                'department' => 'Finance & Ops',
                'salary' => 118000,
                'join_date' => Carbon::now()->subYear()->toDateString(),
                'status' => 'active',
            ],
            [
                'name' => 'Bibek Gurung',
                'email' => 'bibek@saubhagyagroup.com',
                'position' => 'People Operations',
                'department' => 'HR & Admin',
                'salary' => 95000,
                'join_date' => Carbon::now()->subMonths(8)->toDateString(),
                'status' => 'active',
            ],
        ];

        $employeeModels = collect();
        foreach ($employees as $index => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                    'role' => 'employee',
                ]
            );

            $dept = $departmentModels->get($data['department']);

            $employee = HrmEmployee::firstOrCreate(
                ['email' => $data['email']],
                [
                    'user_id' => $user->id,
                    'company_id' => $hrmCompany->id,
                    'department_id' => $dept?->id,
                    'name' => $data['name'],
                    'phone' => '980' . rand(1000000, 9999999),
                    'code' => 'EMP-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'position' => $data['position'],
                    'hire_date' => $data['join_date'],
                    'status' => $data['status'],
                    'basic_salary_npr' => $data['salary'],
                    'salary_type' => 'monthly',
                    'paid_leave_annual' => 15,
                    'paid_leave_sick' => 8,
                    'paid_leave_casual' => 6,
                    'bank_account_number' => '001-' . rand(1000000, 9999999),
                    'bank_name' => 'Saubhagya Bank',
                    'bank_branch' => 'Kathmandu',
                ]
            );

            $employeeModels->push($employee);
        }

        $recentWorkingDays = collect(range(0, 4))->map(fn($offset) => Carbon::now()->subDays($offset)->toDateString());
        foreach ($employeeModels as $employee) {
            foreach ($recentWorkingDays as $day) {
                HrmAttendanceDay::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $day],
                    [
                        'tracked_hours' => 8,
                        'payroll_hours' => 8,
                        'overtime_hours' => $day === $recentWorkingDays->first() ? 1.5 : 0,
                        'source' => 'seed',
                    ]
                );
            }
        }

        $firstEmployee = $employeeModels->first();
        if ($firstEmployee) {
            $anomalyDate = $recentWorkingDays->get(1);
            $attendanceDay = HrmAttendanceDay::where('employee_id', $firstEmployee->id)
                ->where('date', $anomalyDate)
                ->first();

            HrmAttendanceAnomaly::updateOrCreate(
                [
                    'employee_id' => $firstEmployee->id,
                    'date' => $anomalyDate,
                    'anomaly_type' => 'missing_clock_out',
                ],
                [
                    'attendance_day_id' => $attendanceDay?->id,
                    'description' => 'Missing clock-out detected; requires review.',
                    'severity' => 'medium',
                    'reviewed' => false,
                ]
            );
        }

        $periodStart = '2081-08-01';
        $periodEnd = '2081-08-30';

        foreach ($employeeModels as $idx => $employee) {
            HrmPayrollRecord::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'period_start_bs' => $periodStart,
                    'period_end_bs' => $periodEnd,
                ],
                [
                    'total_hours_worked' => 168,
                    'total_days_worked' => 21,
                    'overtime_hours' => $idx === 0 ? 6 : 2,
                    'absent_days' => $idx === 2 ? 1 : 0,
                    'unpaid_leave_days' => 0,
                    'basic_salary' => $employee->basic_salary_npr ?? 90000,
                    'overtime_payment' => $idx === 0 ? 6000 : 2000,
                    'allowances' => ['meal' => 2500, 'internet' => 1500],
                    'allowances_total' => 4000,
                    'gross_salary' => ($employee->basic_salary_npr ?? 90000) + 4000 + ($idx === 0 ? 6000 : 2000),
                    'tax_amount' => 8500,
                    'tax_overridden' => false,
                    'deductions' => ['late_fees' => $idx === 2 ? 1200 : 0],
                    'deductions_total' => $idx === 2 ? 1200 : 0,
                    'unpaid_leave_deduction' => 0,
                    'net_salary' => ($employee->basic_salary_npr ?? 90000) + 4000 + ($idx === 0 ? 6000 : 2000) - 8500 - ($idx === 2 ? 1200 : 0),
                    'status' => $idx === 0 ? 'draft' : ($idx === 1 ? 'approved' : 'paid'),
                    'payment_method' => 'bank_transfer',
                ]
            );
        }

        if ($firstEmployee) {
            HrmLeaveRequest::updateOrCreate(
                [
                    'employee_id' => $firstEmployee->id,
                    'start_date' => Carbon::now()->addDays(3)->toDateString(),
                ],
                [
                    'leave_type' => 'annual',
                    'end_date' => Carbon::now()->addDays(4)->toDateString(),
                    'total_days' => 2,
                    'reason' => 'Family event',
                    'status' => 'pending',
                ]
            );
        }

        $secondEmployee = $employeeModels->get(1);
        if ($secondEmployee) {
            HrmLeaveRequest::updateOrCreate(
                [
                    'employee_id' => $secondEmployee->id,
                    'start_date' => Carbon::now()->subDays(10)->toDateString(),
                ],
                [
                    'leave_type' => 'sick',
                    'end_date' => Carbon::now()->subDays(8)->toDateString(),
                    'total_days' => 3,
                    'reason' => 'Medical rest',
                    'status' => 'approved',
                ]
            );
        }
    }
}
