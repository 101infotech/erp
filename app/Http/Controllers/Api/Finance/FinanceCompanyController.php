<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;

class FinanceCompanyController extends Controller
{
    /**
     * Display a listing of finance companies.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $companies = FinanceCompany::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('pan_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $companies->items(),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
            ]
        ]);
    }

    /**
     * Display the specified finance company.
     */
    public function show(FinanceCompany $company)
    {
        return response()->json([
            'success' => true,
            'data' => $company->load(['accounts', 'transactions', 'sales', 'purchases'])
        ]);
    }
}
