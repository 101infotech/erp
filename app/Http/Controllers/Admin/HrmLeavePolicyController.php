<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmLeavePolicy;
use App\Models\HrmCompany;
use App\Services\LeavePolicyService;

class HrmLeavePolicyController extends Controller
{
    /**
     * Display list of leave policies
     */
    public function index()
    {
        $policies = HrmLeavePolicy::with('company')
            ->where('is_active', true)
            ->orderBy('company_id')
            ->orderBy('leave_type')
            ->paginate(20);

        return view('admin.hrm.leave-policies.index', compact('policies'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $companies = HrmCompany::all();
        return view('admin.hrm.leave-policies.create', compact('companies'));
    }

    /**
     * Store new leave policy
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:hrm_companies,id',
            'policy_name' => 'required|string|max:255',
            'leave_type' => 'required|in:annual,sick,casual,unpaid,period',
            'gender_restriction' => 'required|in:none,male,female',
            'annual_quota' => 'required|numeric|min:0',
            'carry_forward_allowed' => 'boolean',
            'max_carry_forward' => 'nullable|numeric|min:0',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['carry_forward_allowed'] = $request->has('carry_forward_allowed');
        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['is_active'] = $request->has('is_active');
        if (!$validated['carry_forward_allowed'] || empty($validated['max_carry_forward'])) {
            $validated['max_carry_forward'] = 0;
        }

        $policy = HrmLeavePolicy::create($validated);

        // Apply policy to all employees in the company
        $policyService = new LeavePolicyService();
        $updated = $policyService->applyPoliciesToCompany($validated['company_id']);

        return redirect()->route('admin.hrm.leave-policies.index')
            ->with('success', "Leave policy created successfully and applied to {$updated} employee leave balances.");
    }

    /**
     * Show edit form
     */
    public function edit(HrmLeavePolicy $leavePolicy)
    {
        $companies = HrmCompany::all();
        return view('admin.hrm.leave-policies.edit', compact('leavePolicy', 'companies'));
    }

    /**
     * Update leave policy
     */
    public function update(Request $request, HrmLeavePolicy $leavePolicy)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:hrm_companies,id',
            'policy_name' => 'required|string|max:255',
            'leave_type' => 'required|in:annual,sick,casual,unpaid,period',
            'gender_restriction' => 'required|in:none,male,female',
            'annual_quota' => 'required|numeric|min:0',
            'carry_forward_allowed' => 'boolean',
            'max_carry_forward' => 'nullable|numeric|min:0',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if (!$validated['carry_forward_allowed'] || empty($validated['max_carry_forward'])) {
            $validated['max_carry_forward'] = 0;
        }

        $leavePolicy->update($validated);

        // Apply updated policy to all employees in the company if active
        if ($validated['is_active']) {
            $policyService = new LeavePolicyService();
            $updated = $policyService->applyPoliciesToCompany($validated['company_id']);

            return redirect()->route('admin.hrm.leave-policies.index')
                ->with('success', "Leave policy updated successfully and applied to {$updated} employee leave balances.");
        }

        return redirect()->route('admin.hrm.leave-policies.index')
            ->with('success', 'Leave policy updated successfully.');
    }

    /**
     * Delete leave policy
     */
    public function destroy(HrmLeavePolicy $leavePolicy)
    {
        $leavePolicy->delete();

        return redirect()->route('admin.hrm.leave-policies.index')
            ->with('success', 'Leave policy deleted successfully.');
    }
}
