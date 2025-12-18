<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmExpenseClaim;
use App\Models\HrmEmployee;
use App\Models\HrmPayrollRecord;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HrmExpenseClaimController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display list of expense claims
     */
    public function index(Request $request)
    {
        $query = HrmExpenseClaim::with(['employee', 'approver', 'payrollRecord']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by expense type
        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // If not admin, show only own claims
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->role !== 'super_admin') {
            $employee = HrmEmployee::where('user_id', $user->id)->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }
        }

        $claims = $query->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => HrmExpenseClaim::pending()->count(),
            'approved' => HrmExpenseClaim::approved()->count(),
            'paid' => HrmExpenseClaim::paid()->count(),
            'total_pending_amount' => HrmExpenseClaim::pending()->sum('amount'),
            'total_approved_amount' => HrmExpenseClaim::approved()->sum('amount'),
        ];

        return view('admin.hrm.expense-claims.index', compact('claims', 'stats'));
    }

    /**
     * Show the form for creating a new expense claim
     */
    public function create()
    {
        $employees = HrmEmployee::where('status', 'active')
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('admin.hrm.expense-claims.create', compact('employees'));
    }

    /**
     * Store a newly created expense claim
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'expense_type' => 'required|in:travel,accommodation,meals,transportation,supplies,communication,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'project_code' => 'nullable|string|max:50',
            'cost_center' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $path = $file->store('expense-receipts', 'public');
                $validated['receipt_path'] = $path;
            }

            $expenseClaim = HrmExpenseClaim::create($validated);

            // Send notification to admins
            $employee = HrmEmployee::find($validated['employee_id']);
            $this->notificationService->notifyAdmins(
                'New Expense Claim',
                "{$employee->name} has submitted an expense claim for {$validated['currency']} {$validated['amount']} ({$validated['expense_type']})",
                'expense_claim',
                route('admin.hrm.expense-claims.show', $expenseClaim->id)
            );

            DB::commit();

            return redirect()->route('admin.hrm.expense-claims.index')
                ->with('success', 'Expense claim submitted successfully. Claim number: ' . $expenseClaim->claim_number);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Expense claim creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create expense claim. Please try again.');
        }
    }

    /**
     * Display the specified expense claim
     */
    public function show($id)
    {
        $claim = HrmExpenseClaim::with(['employee', 'approver', 'payrollRecord'])->findOrFail($id);
        return view('admin.hrm.expense-claims.show', compact('claim'));
    }

    /**
     * Show the form for editing the specified expense claim
     */
    public function edit($id)
    {
        $claim = HrmExpenseClaim::findOrFail($id);

        // Only allow editing if pending
        if (!$claim->isPending()) {
            return back()->with('error', 'Only pending claims can be edited.');
        }

        $employees = HrmEmployee::where('status', 'active')
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return view('admin.hrm.expense-claims.edit', compact('claim', 'employees'));
    }

    /**
     * Update the specified expense claim
     */
    public function update(Request $request, $id)
    {
        $claim = HrmExpenseClaim::findOrFail($id);

        // Only allow updating if pending
        if (!$claim->isPending()) {
            return back()->with('error', 'Only pending claims can be updated.');
        }

        $validated = $request->validate([
            'expense_type' => 'required|in:travel,accommodation,meals,transportation,supplies,communication,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'project_code' => 'nullable|string|max:50',
            'cost_center' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('receipt')) {
                // Delete old receipt if exists
                if ($claim->receipt_path) {
                    Storage::disk('public')->delete($claim->receipt_path);
                }

                $file = $request->file('receipt');
                $path = $file->store('expense-receipts', 'public');
                $validated['receipt_path'] = $path;
            }

            $claim->update($validated);

            return redirect()->route('admin.hrm.expense-claims.show', $claim->id)
                ->with('success', 'Expense claim updated successfully.');
        } catch (\Exception $e) {
            Log::error('Expense claim update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update expense claim. Please try again.');
        }
    }

    /**
     * Approve an expense claim
     */
    public function approve(Request $request, $id)
    {
        $claim = HrmExpenseClaim::findOrFail($id);

        if (!$claim->isPending()) {
            return back()->with('error', 'Only pending claims can be approved.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $claim->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_by_name' => $user->name,
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes,
            ]);

            // Notify the employee
            $employee = $claim->employee;
            if ($employee->user_id) {
                $this->notificationService->createNotification(
                    $employee->user_id,
                    'Expense Claim Approved',
                    "Your expense claim {$claim->claim_number} for {$claim->formatted_amount} has been approved. It will be included in your next payroll.",
                    'expense_claim',
                    route('admin.hrm.expense-claims.show', $claim->id)
                );
            }

            DB::commit();

            return back()->with('success', 'Expense claim approved successfully. It will be included in the next payroll.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Expense claim approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve expense claim. Please try again.');
        }
    }

    /**
     * Reject an expense claim
     */
    public function reject(Request $request, $id)
    {
        $claim = HrmExpenseClaim::findOrFail($id);

        if (!$claim->isPending()) {
            return back()->with('error', 'Only pending claims can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $claim->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_by_name' => $user->name,
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Notify the employee
            $employee = $claim->employee;
            if ($employee->user_id) {
                $this->notificationService->createNotification(
                    $employee->user_id,
                    'Expense Claim Rejected',
                    "Your expense claim {$claim->claim_number} has been rejected. Reason: {$request->rejection_reason}",
                    'expense_claim',
                    route('admin.hrm.expense-claims.show', $claim->id)
                );
            }

            DB::commit();

            return back()->with('success', 'Expense claim rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Expense claim rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject expense claim. Please try again.');
        }
    }

    /**
     * Get approved claims ready for payroll
     */
    public function getReadyForPayroll(Request $request)
    {
        $employeeId = $request->employee_id;
        $periodStart = $request->period_start;
        $periodEnd = $request->period_end;

        $claims = HrmExpenseClaim::with('employee')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('included_in_payroll', false)
            ->whereBetween('expense_date', [$periodStart, $periodEnd])
            ->get();

        return response()->json([
            'claims' => $claims,
            'total_amount' => $claims->sum('amount'),
        ]);
    }

    /**
     * Remove the specified expense claim
     */
    public function destroy($id)
    {
        $claim = HrmExpenseClaim::findOrFail($id);

        // Only allow deleting if pending
        if (!$claim->isPending()) {
            return back()->with('error', 'Only pending claims can be deleted.');
        }

        try {
            // Delete receipt file if exists
            if ($claim->receipt_path) {
                Storage::disk('public')->delete($claim->receipt_path);
            }

            $claim->delete();

            return redirect()->route('admin.hrm.expense-claims.index')
                ->with('success', 'Expense claim deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Expense claim deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete expense claim. Please try again.');
        }
    }
}
