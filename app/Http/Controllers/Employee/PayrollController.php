<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrmPayrollRecord;
use App\Services\NepalTaxCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    protected $taxService;

    public function __construct(NepalTaxCalculationService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * Display employee's payroll records
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return view('employee.payroll.index', [
                'payrolls' => collect(),
                'employee' => null,
                'message' => 'You are not linked to an employee record.'
            ]);
        }

        $employee = $user->hrmEmployee;

        // Get payroll records
        $payrolls = HrmPayrollRecord::where('employee_id', $employee->id)
            ->orderBy('period_start_bs', 'desc')
            ->paginate(12);

        return view('employee.payroll.index', compact('payrolls', 'employee'));
    }

    /**
     * Display specific payroll details
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            abort(403, 'Not linked to employee record');
        }

        $employee = $user->hrmEmployee;

        $payroll = HrmPayrollRecord::where('employee_id', $employee->id)
            ->findOrFail($id);

        // Get tax breakdown
        $taxBreakdown = $this->taxService->getTaxBreakdownForDisplay($payroll->gross_salary * 12);

        return view('employee.payroll.show', compact('payroll', 'taxBreakdown'));
    }

    /**
     * Download payslip PDF
     */
    public function downloadPayslip($id)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            abort(403, 'Not linked to employee record');
        }

        $employee = $user->hrmEmployee;

        $payroll = HrmPayrollRecord::where('employee_id', $employee->id)
            ->findOrFail($id);

        if (!$payroll->payslip_pdf_path || !file_exists(storage_path('app/' . $payroll->payslip_pdf_path))) {
            abort(404, 'Payslip PDF not found');
        }

        return response()->download(
            storage_path('app/' . $payroll->payslip_pdf_path),
            'payslip-' . $payroll->period_start_bs . '.pdf'
        );
    }

    /**
     * Get payroll data as JSON for API
     */
    public function data(Request $request)
    {
        $user = Auth::user();

        if (!$user->hrmEmployee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not linked to employee record'
            ], 404);
        }

        $employee = $user->hrmEmployee;

        $payrolls = HrmPayrollRecord::where('employee_id', $employee->id)
            ->orderBy('period_start_bs', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $payrolls,
            'employee' => [
                'name' => $employee->full_name,
                'code' => $employee->code,
                'base_salary' => $employee->base_salary,
                'department' => $employee->department->name ?? 'N/A',
                'company' => $employee->company->name ?? 'N/A',
            ]
        ]);
    }
}
