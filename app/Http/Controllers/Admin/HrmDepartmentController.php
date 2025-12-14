<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrmDepartment;
use App\Models\HrmCompany;
use Illuminate\Http\Request;

class HrmDepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = HrmDepartment::with('company')->withCount('employees');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $departments = $query->orderBy('name')->paginate(15);
        $companies = HrmCompany::orderBy('name')->get();

        return view('admin.hrm.departments.index', compact('departments', 'companies'));
    }

    public function create()
    {
        $companies = HrmCompany::orderBy('name')->get();
        return view('admin.hrm.departments.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:hrm_companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        HrmDepartment::create($validated);

        return redirect()
            ->route('admin.hrm.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(HrmDepartment $department)
    {
        $companies = HrmCompany::orderBy('name')->get();
        return view('admin.hrm.departments.edit', compact('department', 'companies'));
    }

    public function update(Request $request, HrmDepartment $department)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:hrm_companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()
            ->route('admin.hrm.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(HrmDepartment $department)
    {
        $department->delete();

        return redirect()
            ->route('admin.hrm.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
