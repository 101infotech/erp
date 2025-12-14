<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrmCompany;
use App\Models\HrmDepartment;

class HrmOrganizationController extends Controller
{
    /**
     * Display organization management page (companies and departments)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'companies');

        $companies = HrmCompany::withCount('employees')
            ->orderBy('name')
            ->get();

        $departments = HrmDepartment::with('company')
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('admin.hrm.organization.index', compact('companies', 'departments', 'tab'));
    }
}
