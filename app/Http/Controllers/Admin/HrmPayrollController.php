<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmEmployee;
use App\Models\HrmPayrollRecord;
use App\Models\HrmCompany;
use App\Models\User;
use App\Services\PayrollCalculationService;
use App\Services\NepalCalendarService;
use App\Services\PayslipPdfService;
use App\Mail\PayrollApprovedMail;
use App\Mail\PayrollSentMail;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HrmPayrollController extends Controller
{
    protected PayrollCalculationService $payrollService;
    protected NepalCalendarService $calendarService;
    protected PayslipPdfService $pdfService;
    protected NotificationService $notificationService;

    public function __construct(
        PayrollCalculationService $payrollService,
        NepalCalendarService $calendarService,
        PayslipPdfService $pdfService,
        NotificationService $notificationService
    ) {
        $this->payrollService = $payrollService;
        $this->calendarService = $calendarService;
        $this->pdfService = $pdfService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display list of payroll records
     */
    public function index(Request $request)
    {
        $query = HrmPayrollRecord::with('employee', 'approver');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        // Filter by period
        if ($request->filled('period_start') && $request->filled('period_end')) {
            $query->whereBetween('period_start_bs', [$request->period_start, $request->period_end]);
        }

        $payrolls = $query->orderBy('period_start_bs', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $companies = HrmCompany::all();
        $employees = HrmEmployee::select('id', 'name')->get();

        return view('admin.hrm.payroll.index', compact('payrolls', 'companies', 'employees'));
    }

    /**
     * Show payroll generation form
     */
    public function create()
    {
        $employees = HrmEmployee::where('status', 'active')
            ->select('id', 'name', 'code', 'company_id', 'basic_salary_npr')
            ->with('company:id,name')
            ->orderBy('name')
            ->get();

        $companies = HrmCompany::all();

        // Log for debugging
        \Log::info('Payroll Create - Employee Count: ' . $employees->count());
        if ($employees->isEmpty()) {
            \Log::warning('No active employees found for payroll generation');
        }

        // Check how many employees have salary configured
        $employeesWithSalary = $employees->filter(fn($e) => $e->basic_salary_npr > 0)->count();
        $employeesWithoutSalary = $employees->count() - $employeesWithSalary;

        if ($employeesWithoutSalary > 0) {
            \Log::warning("Payroll Create - {$employeesWithoutSalary} employees without salary configuration");
        }

        return view('admin.hrm.payroll.create', compact('employees', 'companies', 'employeesWithSalary', 'employeesWithoutSalary'));
    }

    /**
     * Generate payroll records
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:hrm_employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'month_total_days' => 'nullable|integer|min:29|max:32',
            'standard_working_hours' => 'nullable|numeric|min:1|max:24',
        ]);

        // Convert AD dates to Carbon and BS format
        $periodStart = Carbon::parse($validated['period_start']);
        $periodEnd = Carbon::parse($validated['period_end']);

        // Convert to BS for storage
        $periodStartBs = nepali_date($periodStart->format('Y-m-d'));
        $periodEndBs = nepali_date($periodEnd->format('Y-m-d'));

        if (!$periodStartBs || !$periodEndBs) {
            return back()->withErrors(['period_start' => 'Unable to convert dates to Nepali calendar']);
        }

        // Check for date collisions
        $collisionCheck = HrmPayrollRecord::checkDateCollisions(
            $validated['employee_ids'],
            $periodStart->format('Y-m-d'),
            $periodEnd->format('Y-m-d')
        );

        if ($collisionCheck['has_collision']) {
            // Return with collision details
            return back()
                ->withInput()
                ->with('collision_error', true)
                ->with('collisions', $collisionCheck['collisions'])
                ->withErrors(['period_start' => 'Date collision detected! Some employees already have payroll records for this period.']);
        }

        // Get month total days and working hours (use defaults if not provided)
        $monthTotalDays = $validated['month_total_days'] ?? null; // null means auto-detect
        $standardWorkingHours = $validated['standard_working_hours'] ?? 8.00;

        // Log payroll generation attempt
        \Log::info('Generating payroll', [
            'employee_count' => count($validated['employee_ids']),
            'period' => $periodStartBs . ' to ' . $periodEndBs,
        ]);

        $result = $this->payrollService->generateBulkPayroll(
            $validated['employee_ids'],
            $periodStart,
            $periodEnd,
            $periodStartBs,
            $periodEndBs,
            $monthTotalDays,
            $standardWorkingHours
        );

        $successCount = count($result['success']);
        $failedCount = count($result['failed']);

        // Log results
        \Log::info('Payroll generation completed', [
            'success' => $successCount,
            'failed' => $failedCount,
        ]);

        if ($successCount > 0) {
            session()->flash('success', "Successfully generated {$successCount} payroll records.");
        }

        if ($failedCount > 0) {
            $failedNames = collect($result['failed'])->pluck('name')->join(', ');
            $failedReasons = collect($result['failed'])->map(fn($f) => $f['name'] . ': ' . $f['reason'])->join(' | ');
            \Log::warning('Payroll generation failures', ['failures' => $failedReasons]);
            session()->flash('warning', "Failed to generate for {$failedCount} employees: {$failedNames}");
        }

        if ($successCount === 0 && $failedCount === 0) {
            \Log::error('Payroll generation returned no results');
            session()->flash('error', 'No payroll records were generated. Please check the logs for details.');
        }

        return redirect()->route('admin.hrm.payroll.index');
    }

    /**
     * Display single payroll record (payslip)
     */
    public function show($id)
    {
        $payroll = HrmPayrollRecord::with([
            'employee.company',
            'employee.department',
            'approver'
        ])->findOrFail($id);

        // Get anomalies for this payroll period (convert BS to AD for date comparison)
        $periodStartAd = english_date($payroll->period_start_bs);
        $periodEndAd = english_date($payroll->period_end_bs);

        $anomalies = \App\Models\HrmAttendanceAnomaly::where('employee_id', $payroll->employee_id)
            ->whereBetween('date', [$periodStartAd, $periodEndAd])
            ->orderBy('severity', 'desc')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.hrm.payroll.show', compact('payroll', 'anomalies'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $payroll = HrmPayrollRecord::with('employee')->findOrFail($id);

        if ($payroll->status !== 'draft') {
            return redirect()->route('admin.hrm.payroll.show', $id)
                ->with('error', 'Only draft payrolls can be edited.');
        }

        return view('admin.hrm.payroll.edit', compact('payroll'));
    }

    /**
     * Update payroll record
     */
    public function update(Request $request, $id)
    {
        $payroll = HrmPayrollRecord::findOrFail($id);

        if ($payroll->status !== 'draft') {
            return redirect()->route('admin.hrm.payroll.show', $id)
                ->with('error', 'Only draft payrolls can be edited.');
        }

        $validated = $request->validate([
            'overtime_payment' => 'nullable|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'bonus_reason' => 'nullable|string',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
            'tax_amount' => 'nullable|numeric|min:0',
            'tax_override_reason' => 'nullable|string|required_with:tax_amount',
            'hourly_deduction_amount' => 'nullable|numeric|min:0',
            'hourly_deduction_approved' => 'nullable|boolean',
            'advance_payment' => 'nullable|numeric|min:0',
            'advance_payment_reason' => 'nullable|string',
            'advance_payment_date' => 'nullable|date',
            'verbal_leave_days' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($payroll, $validated) {
            // Update overtime payment
            if (isset($validated['overtime_payment'])) {
                $payroll->overtime_payment = $validated['overtime_payment'];
            }

            // Update bonus as an allowance
            if (isset($validated['bonus_amount']) && $validated['bonus_amount'] > 0) {
                $existingAllowances = $payroll->allowances ?? [];
                // Remove any existing bonus entry
                $existingAllowances = array_filter($existingAllowances, function ($a) {
                    return !isset($a['name']) || !str_contains(strtolower($a['name']), 'bonus');
                });
                // Add new bonus
                $existingAllowances[] = [
                    'name' => $validated['bonus_reason'] ?: 'Bonus',
                    'amount' => $validated['bonus_amount']
                ];
                $payroll->allowances = array_values($existingAllowances);
            }

            // Update allowances
            if (isset($validated['allowances'])) {
                $payroll->allowances = $validated['allowances'];
                $payroll->allowances_total = collect($validated['allowances'])->sum('amount');
            } elseif (isset($validated['bonus_amount'])) {
                // Recalculate allowances total if bonus was added
                $payroll->allowances_total = collect($payroll->allowances)->sum('amount');
            }

            // Update deductions
            if (isset($validated['deductions'])) {
                $payroll->deductions = $validated['deductions'];
                $payroll->deductions_total = collect($validated['deductions'])->sum('amount');
            }

            // Handle hourly deduction approval
            if (isset($validated['hourly_deduction_amount'])) {
                $payroll->hourly_deduction_amount = $validated['hourly_deduction_amount'];
                $payroll->hourly_deduction_approved = $validated['hourly_deduction_approved'] ?? true;
            } elseif (isset($validated['hourly_deduction_approved']) && !$validated['hourly_deduction_approved']) {
                // Admin waived the hourly deduction
                $payroll->hourly_deduction_amount = 0;
                $payroll->hourly_deduction_approved = false;
            }

            // Handle verbal leave days and recalculate required/missing hours
            if (isset($validated['verbal_leave_days'])) {
                $verbalLeaveDays = (int) $validated['verbal_leave_days'];
                $payroll->verbal_leave_days = $verbalLeaveDays;

                // Recalculate total payable days (including verbal leave as paid days)
                // Payable days = days worked + paid leave + verbal leave + weekends
                $periodStart = \Carbon\Carbon::parse($payroll->period_start_ad);
                $periodEnd = \Carbon\Carbon::parse($payroll->period_end_ad);
                $totalDays = $periodStart->diffInDays($periodEnd) + 1;

                // Count weekends
                $weekends = 0;
                $current = $periodStart->copy();
                while ($current->lte($periodEnd)) {
                    if ($current->isSaturday()) {
                        $weekends++;
                    }
                    $current->addDay();
                }

                // Total payable days = worked days + paid leave + verbal leave + weekends
                // (Absent days and unpaid leave are NOT payable)
                $totalPayableDays = $payroll->total_days_worked
                    + ($payroll->paid_leave_days_used ?? 0)
                    + $verbalLeaveDays
                    + $weekends;

                // Update total payable days
                $payroll->total_payable_days = $totalPayableDays;

                // IMPORTANT: Required hours = Payable Days × Standard Hours
                // This is the key fix - we require hours only for days we're paying for
                $requiredHours = $totalPayableDays * $payroll->standard_working_hours_per_day;
                $missingHours = max(0, $requiredHours - $payroll->total_hours_worked);

                // Update payroll record
                $payroll->total_working_hours_required = round($requiredHours, 2);
                $payroll->total_working_hours_missing = round($missingHours, 2);

                // Recalculate suggested hourly deduction
                if ($missingHours > 0) {
                    $dailyRate = ($payroll->employee->basic_salary_npr ?? 0) / $payroll->month_total_days;
                    $hourlyRate = $dailyRate / $payroll->standard_working_hours_per_day;
                    $payroll->hourly_deduction_suggested = round($hourlyRate * $missingHours, 2);
                } else {
                    $payroll->hourly_deduction_suggested = 0;
                    $payroll->hourly_deduction_amount = 0;
                    $payroll->hourly_deduction_approved = false;
                }
            }

            // Handle advance payment
            if (isset($validated['advance_payment']) && $validated['advance_payment'] > 0) {
                $payroll->advance_payment = $validated['advance_payment'];
                $payroll->advance_payment_details = [
                    'amount' => $validated['advance_payment'],
                    'reason' => $validated['advance_payment_reason'] ?? '',
                    'date' => $validated['advance_payment_date'] ?? now()->format('Y-m-d'),
                    'recorded_at' => now()->toIso8601String(),
                    'recorded_by' => Auth::user()?->name ?? 'Admin',
                ];
            }

            // Recalculate gross salary
            $payroll->gross_salary = $payroll->basic_salary
                + $payroll->overtime_payment
                + $payroll->allowances_total;

            // Handle tax override
            if (isset($validated['tax_amount'])) {
                $payroll->tax_amount = $validated['tax_amount'];
                $payroll->tax_overridden = true;
                $payroll->tax_override_reason = $validated['tax_override_reason'];
            }

            // Recalculate net salary (including hourly deduction and advance payment)
            $payroll->net_salary = $payroll->gross_salary
                - $payroll->tax_amount
                - $payroll->deductions_total
                - $payroll->unpaid_leave_deduction
                - ($payroll->hourly_deduction_approved ? $payroll->hourly_deduction_amount : 0)
                - $payroll->advance_payment;

            $payroll->save();
        });

        return redirect()->route('admin.hrm.payroll.show', $id)
            ->with('success', 'Payroll updated successfully.');
    }

    /**
     * Approve payroll record
     */
    public function approve(Request $request, $id)
    {
        $payroll = HrmPayrollRecord::with('employee')->findOrFail($id);

        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Only draft payrolls can be approved.');
        }

        // Check if anomalies are reviewed
        if (!$payroll->anomalies_reviewed && !empty($payroll->anomalies)) {
            return back()->with('error', 'Please review all anomalies before approving.');
        }

        DB::transaction(function () use ($payroll) {
            $user = Auth::user();

            $payroll->update([
                'status' => 'approved',
                'approved_by' => $user?->id,
                'approved_at' => now(),
                'approved_by_name' => $user?->name ?? 'Admin',
            ]);

            // Generate PDF payslip
            try {
                $pdfPath = $this->pdfService->generatePayslipPdf($payroll);

                // Store PDF path in payroll record
                $payroll->update(['payslip_pdf_path' => $pdfPath]);

                // Send email notification to employee (if email exists)
                if ($payroll->employee->email) {
                    Mail::to($payroll->employee->email)
                        ->queue(new PayrollApprovedMail($payroll, $pdfPath));
                }
            } catch (\Exception $e) {
                Log::error('Failed to generate PDF or send email for payroll #' . $payroll->id . ': ' . $e->getMessage());
                // Don't fail the approval if PDF/email fails
            }
        });

        return redirect()->route('admin.hrm.payroll.show', $id)
            ->with('success', 'Payroll approved successfully. Email notification sent to employee.');
    }

    /**
     * Mark anomalies as reviewed
     */
    public function reviewAnomalies(Request $request, $id)
    {
        $payroll = HrmPayrollRecord::findOrFail($id);

        $payroll->update([
            'anomalies_reviewed' => true,
        ]);

        return back()->with('success', 'Anomalies marked as reviewed.');
    }

    /**
     * Mark payroll as paid
     */
    public function markAsPaid(Request $request, $id)
    {
        $payroll = HrmPayrollRecord::findOrFail($id);

        if ($payroll->status !== 'approved') {
            return back()->with('error', 'Only approved payrolls can be marked as paid.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,cheque',
            'transaction_reference' => 'nullable|string',
        ]);

        $payroll->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $validated['payment_method'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
        ]);

        return back()->with('success', 'Payroll marked as paid.');
    }

    /**
     * Mark payroll as sent and send email to employee
     */
    public function markAsSent(Request $request, $id)
    {
        $payroll = HrmPayrollRecord::with('employee')->findOrFail($id);

        if ($payroll->status !== 'approved' && $payroll->status !== 'paid') {
            return back()->with('error', 'Only approved or paid payrolls can be marked as sent.');
        }

        if (!$payroll->employee->email) {
            return back()->with('error', 'Employee email not found. Cannot send payslip.');
        }

        try {
            DB::transaction(function () use ($payroll) {
                $user = Auth::user();

                // If PDF doesn't exist, generate it
                if (!$payroll->payslip_pdf_path || !file_exists($payroll->payslip_pdf_path)) {
                    $pdfPath = $this->pdfService->generatePayslipPdf($payroll);
                    $payroll->update(['payslip_pdf_path' => $pdfPath]);
                }

                // Update payroll record
                $payroll->update([
                    'sent_at' => now(),
                    'sent_by' => $user?->id,
                    'sent_by_name' => $user?->name ?? 'Admin',
                ]);

                // Send email notification to employee
                Mail::to($payroll->employee->email)
                    ->queue(new PayrollSentMail($payroll, $payroll->payslip_pdf_path));

                // Create notification for employee
                if ($payroll->employee->user_id) {
                    $this->notificationService->createNotification(
                        $payroll->employee->user_id,
                        'payroll',
                        'Payslip Sent',
                        "Your payslip for {$payroll->period_start_bs} - {$payroll->period_end_bs} has been sent to your email.",
                        route('employee.payroll.show', $payroll->id)
                    );
                }
            });

            return back()->with('success', 'Payslip sent successfully to employee email.');
        } catch (\Exception $e) {
            Log::error('Failed to send payslip for payroll #' . $payroll->id . ': ' . $e->getMessage());
            return back()->with('error', 'Failed to send payslip: ' . $e->getMessage());
        }
    }

    /**
     * Download payslip PDF
     */
    public function downloadPdf($id)
    {
        $payroll = HrmPayrollRecord::findOrFail($id);

        // If PDF doesn't exist, generate it
        if (!$payroll->payslip_pdf_path || !file_exists($payroll->payslip_pdf_path)) {
            try {
                $pdfPath = $this->pdfService->generatePayslipPdf($payroll);
                $payroll->update(['payslip_pdf_path' => $pdfPath]);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
            }
        }

        $filename = 'Payslip_' . str_replace(['/', ' '], '_', $payroll->period_start_bs) . '.pdf';

        return response()->download($payroll->payslip_pdf_path, $filename);
    }

    /**
     * Delete payroll record
     */
    public function destroy($id)
    {
        $payroll = HrmPayrollRecord::findOrFail($id);

        // Only allow deletion of draft payrolls
        if ($payroll->status !== 'draft') {
            return back()->with('error', 'Only draft payrolls can be deleted. Please reject approved/paid payrolls instead.');
        }

        DB::transaction(function () use ($payroll) {
            // Delete PDF file if exists
            if ($payroll->payslip_pdf_path && file_exists($payroll->payslip_pdf_path)) {
                try {
                    unlink($payroll->payslip_pdf_path);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete PDF file for payroll #' . $payroll->id . ': ' . $e->getMessage());
                }
            }

            // Delete the payroll record
            $payroll->delete();
        });

        return redirect()->route('admin.hrm.payroll.index')
            ->with('success', 'Payroll record deleted successfully.');
    }
}
