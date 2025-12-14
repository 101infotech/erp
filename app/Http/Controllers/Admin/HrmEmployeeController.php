<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrmEmployee;
use App\Models\HrmCompany;
use App\Models\HrmDepartment;
use App\Services\JibblePeopleService;
use App\Services\LeavePolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HrmEmployeeController extends Controller
{
    public function __construct(private readonly JibblePeopleService $jibblePeopleService) {}

    public function index(Request $request)
    {
        $query = HrmEmployee::with(['company', 'department', 'user']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name')->paginate(15);
        $companies = HrmCompany::orderBy('name')->get();
        $departments = HrmDepartment::orderBy('name')->get();

        return view('admin.hrm.employees.index', compact('employees', 'companies', 'departments'));
    }

    public function create()
    {
        $companies = HrmCompany::orderBy('name')->get();
        $departments = HrmDepartment::orderBy('name')->get();
        return view('admin.hrm.employees.create', compact('companies', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:hrm_companies,id',
            'department_id' => 'nullable|exists:hrm_departments,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:hrm_employees',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // Ensure date fields are properly formatted (YYYY-MM-DD only, no time)
        $dateFields = ['hire_date', 'date_of_birth'];
        foreach ($dateFields as $field) {
            if (isset($validated[$field]) && $validated[$field]) {
                $validated[$field] = date('Y-m-d', strtotime($validated[$field]));
            }
        }

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('hrm/avatars', 'public');
        }

        $employee = HrmEmployee::create($validated);

        // Apply leave policies to the new employee
        $policyService = new LeavePolicyService();
        $policyService->applyPoliciesToEmployee($employee);

        // Sync to Jibble if requested
        if ($request->boolean('sync_to_jibble') && $employee->email) {
            try {
                $jibbleId = $this->jibblePeopleService->createPerson($employee);
                if ($jibbleId) {
                    $employee->update(['jibble_person_id' => $jibbleId]);
                }
            } catch (\Exception $e) {
                // Log but don't fail the request
                Log::error('Failed to sync employee to Jibble', ['error' => $e->getMessage()]);
            }
        }

        return redirect()
            ->route('admin.hrm.employees.index')
            ->with('success', 'Employee created successfully with leave balances applied.');
    }

    public function show(HrmEmployee $employee)
    {
        $employee->load(['company', 'department', 'user', 'attendanceDays' => function ($query) {
            $query->orderBy('date', 'desc')->limit(30);
        }]);

        return view('admin.hrm.employees.show', compact('employee'));
    }

    public function edit(HrmEmployee $employee)
    {
        $companies = HrmCompany::orderBy('name')->get();
        $departments = HrmDepartment::orderBy('name')->get();
        return view('admin.hrm.employees.edit', compact('employee', 'companies', 'departments'));
    }

    public function update(Request $request, HrmEmployee $employee)
    {
        $validated = $request->validate([
            // Basic Info
            'company_id' => 'required|exists:hrm_companies,id',
            'department_id' => 'nullable|exists:hrm_departments,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:hrm_employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',

            // Personal Info
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'address' => 'nullable|string',

            // Emergency Contact
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',

            // Employment Details
            'employment_type' => 'nullable|in:full-time,part-time,contract,intern',
            'working_days_per_week' => 'nullable|integer|min:1|max:7',

            // Contract & Salary
            'salary_type' => 'nullable|in:monthly,hourly,daily',
            'salary_amount' => 'nullable|numeric|min:0',
            'allowances_amount' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'probation_period_months' => 'nullable|integer|min:0|max:12',

            // Banking & Tax
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'tax_regime' => 'nullable|string|max:255',

            // Leave Balances
            'sick_leave_balance' => 'nullable|numeric|min:0',
            'casual_leave_balance' => 'nullable|numeric|min:0',
            'annual_leave_balance' => 'nullable|numeric|min:0',

            'avatar' => 'nullable|image|max:2048',
        ]);

        // Sync basic_salary_npr with salary_amount
        if (isset($validated['salary_amount'])) {
            $validated['basic_salary_npr'] = $validated['salary_amount'];
        }

        // Sync allowances_amount with allowances JSON
        if (isset($validated['allowances_amount'])) {
            $validated['allowances'] = ['total' => $validated['allowances_amount']];
        }

        // Ensure date fields are properly formatted (YYYY-MM-DD only, no time)
        $dateFields = ['contract_start_date', 'contract_end_date', 'hire_date', 'date_of_birth'];
        foreach ($dateFields as $field) {
            if (isset($validated[$field]) && $validated[$field]) {
                // Convert to Y-m-d format to ensure no time component
                $validated[$field] = date('Y-m-d', strtotime($validated[$field]));
            }
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('hrm/avatars', 'public');
            $validated['avatar_url'] = Storage::url($validated['avatar']);
        }

        $employee->update($validated);

        // Sync to Jibble if requested
        if ($request->boolean('sync_to_jibble')) {
            try {
                $this->jibblePeopleService->updatePerson($employee);
            } catch (\Exception $e) {
                Log::error('Failed to sync employee to Jibble', ['error' => $e->getMessage()]);
            }
        }

        return redirect()
            ->route('admin.hrm.employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(HrmEmployee $employee)
    {
        // Delete avatar if exists
        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        // Delete from Jibble if requested
        if ($employee->jibble_person_id) {
            try {
                $this->jibblePeopleService->deletePerson($employee);
            } catch (\Exception $e) {
                Log::error('Failed to delete employee from Jibble', ['error' => $e->getMessage()]);
            }
        }

        $employee->delete();

        return redirect()
            ->route('admin.hrm.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Sync employees from Jibble
     */
    public function syncFromJibble()
    {
        try {
            $count = $this->jibblePeopleService->syncEmployees();
            return redirect()
                ->route('admin.hrm.employees.index')
                ->with('success', "Successfully synced {$count} employees from Jibble.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.hrm.employees.index')
                ->with('error', 'Failed to sync from Jibble: ' . $e->getMessage());
        }
    }
}
