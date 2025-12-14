<?php

namespace App\Services;

use App\Models\HrmPayrollRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PayslipPdfService
{
    /**
     * Generate PDF for a payroll record
     *
     * @param HrmPayrollRecord $payrollRecord
     * @return string Path to generated PDF
     */
    public function generatePayslipPdf(HrmPayrollRecord $payrollRecord): string
    {
        // Load payroll record with relationships
        $payrollRecord->load([
            'employee.company',
            'employee.department',
            'company',
            'anomalies'
        ]);

        // Prepare data for PDF
        $data = $this->preparePayslipData($payrollRecord);

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.payslip', $data);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Generate filename
        $filename = $this->generateFilename($payrollRecord);

        // Store PDF in storage
        $path = 'payslips/' . $filename;
        Storage::put($path, $pdf->output());

        return Storage::path($path);
    }

    /**
     * Prepare data for payslip PDF
     *
     * @param HrmPayrollRecord $payrollRecord
     * @return array
     */
    protected function preparePayslipData(HrmPayrollRecord $payrollRecord): array
    {
        $employee = $payrollRecord->employee;

        // Calculate earnings
        $earnings = [
            ['label' => 'Basic Salary', 'amount' => $payrollRecord->basic_salary],
        ];

        if ($payrollRecord->overtime_payment > 0) {
            $earnings[] = [
                'label' => "Overtime ({$payrollRecord->overtime_hours} hrs @ NPR " .
                    number_format($payrollRecord->overtime_rate, 2) . ")",
                'amount' => $payrollRecord->overtime_payment
            ];
        }

        // Add allowances
        if (!empty($payrollRecord->allowances)) {
            foreach ($payrollRecord->allowances as $allowance) {
                $earnings[] = [
                    'label' => $allowance['name'],
                    'amount' => $allowance['amount']
                ];
            }
        }

        // Calculate deductions
        $deductions = [
            ['label' => 'Tax (TDS)', 'amount' => $payrollRecord->tax_amount],
        ];

        // Add deductions
        if (!empty($payrollRecord->deductions)) {
            foreach ($payrollRecord->deductions as $deduction) {
                $deductions[] = [
                    'label' => $deduction['name'],
                    'amount' => $deduction['amount']
                ];
            }
        }

        // Attendance summary
        $attendanceSummary = [
            'Total Days' => $payrollRecord->total_days,
            'Working Days' => $payrollRecord->working_days,
            'Present Days' => $payrollRecord->present_days,
            'Absent Days' => $payrollRecord->absent_days,
            'Leave Days' => $payrollRecord->leave_days,
            'Weekend Days' => $payrollRecord->weekend_days,
            'Holiday Days' => $payrollRecord->holiday_days,
        ];

        // Get anomalies (cast as array, not collection)
        $anomalies = [];
        if (is_array($payrollRecord->anomalies)) {
            $anomalies = array_map(function ($anomaly) {
                return [
                    'type' => $anomaly['anomaly_type'] ?? $anomaly['type'] ?? 'unknown',
                    'severity' => $anomaly['severity'] ?? 'info',
                    'description' => $anomaly['description'] ?? '',
                    'date' => $anomaly['date_bs'] ?? $anomaly['date'] ?? null,
                ];
            }, $payrollRecord->anomalies);
        }

        return [
            'payrollRecord' => $payrollRecord,
            'employee' => $employee,
            'company' => $payrollRecord->company ?? $employee->company,
            'department' => $employee->department,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'attendanceSummary' => $attendanceSummary,
            'anomalies' => $anomalies,
            'totalEarnings' => $payrollRecord->gross_salary,
            'totalDeductions' => $payrollRecord->tax_amount + array_sum(array_column($payrollRecord->deductions ?? [], 'amount')),
            'netSalary' => $payrollRecord->net_salary,
        ];
    }

    /**
     * Generate unique filename for payslip
     *
     * @param HrmPayrollRecord $payrollRecord
     * @return string
     */
    protected function generateFilename(HrmPayrollRecord $payrollRecord): string
    {
        $employeeId = $payrollRecord->employee_id;
        $period = str_replace(['/', ' '], '_', $payrollRecord->period_start_bs);

        return "payslip_{$employeeId}_{$period}_{$payrollRecord->id}.pdf";
    }

    /**
     * Delete payslip PDF
     *
     * @param string $path
     * @return bool
     */
    public function deletePayslipPdf(string $path): bool
    {
        if (Storage::exists($path)) {
            return Storage::delete($path);
        }

        return false;
    }

    /**
     * Get payslip PDF URL
     *
     * @param string $path
     * @return string|null
     */
    public function getPayslipUrl(string $path): ?string
    {
        if (Storage::exists($path)) {
            return Storage::url($path);
        }

        return null;
    }
}
