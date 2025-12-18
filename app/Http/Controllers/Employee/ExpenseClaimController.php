<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrmEmployee;
use App\Models\HrmExpenseClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExpenseClaimController extends Controller
{
    public function index(Request $request)
    {
        $employee = $this->currentEmployee();
        if (!$employee) {
            return view('employee.expense-claims.index', [
                'claims' => collect(),
                'employeeMissing' => true,
            ]);
        }

        $query = HrmExpenseClaim::where('employee_id', $employee->id)->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        $claims = $query->paginate(15);

        return view('employee.expense-claims.index', compact('claims'));
    }

    public function create()
    {
        $employee = $this->currentEmployee();
        if (!$employee) {
            return redirect()->route('employee.expense-claims.index')
                ->with('error', 'No employee profile linked to your account.');
        }

        return view('employee.expense-claims.create');
    }

    public function store(Request $request)
    {
        $employee = $this->currentEmployee();
        if (!$employee) {
            return redirect()->route('employee.expense-claims.index')
                ->with('error', 'No employee profile linked to your account.');
        }

        $validated = $request->validate([
            'expense_type' => 'required|in:travel,accommodation,meals,transportation,supplies,communication,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $data = array_merge($validated, [
                'employee_id' => $employee->id,
                'status' => 'pending',
            ]);

            if ($request->hasFile('receipt')) {
                $data['receipt_path'] = $request->file('receipt')->store('expense-receipts', 'public');
            }

            HrmExpenseClaim::create($data);

            DB::commit();

            return redirect()
                ->route('employee.expense-claims.index')
                ->with('success', 'Expense claim submitted for review.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee expense claim failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to submit claim. Please try again.');
        }
    }

    private function currentEmployee(): ?HrmEmployee
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return HrmEmployee::where('user_id', $user->id)->first();
    }
}
