<?php

namespace App\Services;

use App\Models\HrmPayrollRecord;
use App\Models\FinanceTransaction;
use App\Models\FinanceCompany;
use App\Models\FinanceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollFinanceIntegrationService
{
    /**
     * Create finance transaction when payroll is approved
     * 
     * @param HrmPayrollRecord $payroll
     * @return FinanceTransaction|null
     */
    public function createFinanceTransactionForPayroll(HrmPayrollRecord $payroll): ?FinanceTransaction
    {
        try {
            return DB::transaction(function () use ($payroll) {
                $employee = $payroll->employee;

                // Get the finance company associated with the HR company
                $financeCompany = $this->getFinanceCompanyForEmployee($employee);

                if (!$financeCompany) {
                    Log::warning("No finance company found for employee {$employee->id} in payroll {$payroll->id}");
                    return null;
                }

                // Get or create payroll expense category
                $category = $this->getOrCreatePayrollCategory($financeCompany->id);

                // Generate transaction number
                $transactionNumber = $this->generateTransactionNumber($financeCompany->id);

                // Create the finance transaction
                $transaction = FinanceTransaction::create([
                    'company_id' => $financeCompany->id,
                    'transaction_number' => $transactionNumber,
                    'transaction_date_bs' => $payroll->period_end_bs,
                    'transaction_type' => 'expense',
                    'category_id' => $category->id,
                    'amount' => $payroll->net_salary,
                    'description' => "Payroll expense for {$employee->name} ({$employee->code}) - Period: {$payroll->period_start_bs} to {$payroll->period_end_bs}",
                    'payment_method' => $payroll->payment_method ?? 'bank_transfer',
                    'reference_number' => "PAYROLL-{$payroll->id}",
                    'notes' => json_encode([
                        'payroll_id' => $payroll->id,
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'employee_code' => $employee->code,
                        'basic_salary' => $payroll->basic_salary,
                        'gross_salary' => $payroll->gross_salary,
                        'deductions_total' => $payroll->deductions_total,
                        'net_salary' => $payroll->net_salary,
                        'period_start' => $payroll->period_start_bs,
                        'period_end' => $payroll->period_end_bs,
                    ]),
                    'status' => $payroll->status === 'paid' ? 'completed' : 'approved',
                    'created_by_user_id' => $payroll->approved_by,
                ]);

                Log::info("Finance transaction {$transaction->transaction_number} created for payroll {$payroll->id}");

                return $transaction;
            });
        } catch (\Exception $e) {
            Log::error("Failed to create finance transaction for payroll {$payroll->id}: " . $e->getMessage());
            // Don't throw exception - we don't want to block payroll approval if finance integration fails
            return null;
        }
    }

    /**
     * Update finance transaction when payroll is marked as paid
     * 
     * @param HrmPayrollRecord $payroll
     * @return bool
     */
    public function updateFinanceTransactionForPaidPayroll(HrmPayrollRecord $payroll): bool
    {
        try {
            // Find existing finance transaction for this payroll
            $transaction = FinanceTransaction::where('reference_number', "PAYROLL-{$payroll->id}")->first();

            if (!$transaction) {
                // If no transaction exists, create one
                $this->createFinanceTransactionForPayroll($payroll);
                return true;
            }

            // Update transaction status and payment details
            $transaction->update([
                'status' => 'completed',
                'payment_method' => $payroll->payment_method ?? $transaction->payment_method,
            ]);

            Log::info("Finance transaction {$transaction->transaction_number} updated to completed for payroll {$payroll->id}");

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update finance transaction for payroll {$payroll->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create bulk finance transactions for multiple payrolls
     * 
     * @param array $payrollIds
     * @return array
     */
    public function createBulkFinanceTransactions(array $payrollIds): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($payrollIds as $payrollId) {
            try {
                $payroll = HrmPayrollRecord::with('employee')->find($payrollId);

                if (!$payroll) {
                    $results['failed'][] = [
                        'id' => $payrollId,
                        'reason' => 'Payroll record not found',
                    ];
                    continue;
                }

                if ($payroll->status !== 'approved' && $payroll->status !== 'paid') {
                    $results['failed'][] = [
                        'id' => $payrollId,
                        'reason' => 'Payroll not approved or paid',
                    ];
                    continue;
                }

                $transaction = $this->createFinanceTransactionForPayroll($payroll);

                if ($transaction) {
                    $results['success'][] = [
                        'payroll_id' => $payrollId,
                        'transaction_id' => $transaction->id,
                        'transaction_number' => $transaction->transaction_number,
                    ];
                } else {
                    $results['failed'][] = [
                        'id' => $payrollId,
                        'reason' => 'Failed to create finance transaction',
                    ];
                }
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'id' => $payrollId,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get finance company for an employee
     * 
     * @param \App\Models\HrmEmployee $employee
     * @return FinanceCompany|null
     */
    protected function getFinanceCompanyForEmployee($employee): ?FinanceCompany
    {
        // First, try to get finance company from HR company relationship
        if ($employee->company && $employee->company->finance_company_id) {
            return FinanceCompany::find($employee->company->finance_company_id);
        }

        // Fallback: Get any active finance company (preferably holding company)
        return FinanceCompany::active()->holding()->first()
            ?? FinanceCompany::active()->first();
    }

    /**
     * Get or create payroll expense category
     * 
     * @param int $companyId
     * @return FinanceCategory
     */
    protected function getOrCreatePayrollCategory(int $companyId): FinanceCategory
    {
        $category = FinanceCategory::where('company_id', $companyId)
            ->where('name', 'Payroll Expenses')
            ->first();

        if (!$category) {
            $category = FinanceCategory::create([
                'company_id' => $companyId,
                'name' => 'Payroll Expenses',
                'type' => 'expense',
                'description' => 'Employee salary and payroll expenses',
                'is_active' => true,
            ]);

            Log::info("Created payroll expense category for company {$companyId}");
        }

        return $category;
    }

    /**
     * Generate unique transaction number
     * 
     * @param int $companyId
     * @return string
     */
    protected function generateTransactionNumber(int $companyId): string
    {
        $prefix = 'TRX-PAY';
        $date = now()->format('Ymd');

        // Get the last transaction number for today
        $lastTransaction = FinanceTransaction::where('company_id', $companyId)
            ->where('transaction_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('transaction_number', 'desc')
            ->first();

        if ($lastTransaction) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastTransaction->transaction_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$date}-{$newNumber}";
    }
}
