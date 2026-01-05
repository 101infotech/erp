<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceSale;
use App\Models\FinanceCompany;
use App\Models\FinanceCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceSaleController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $fiscalYear = $request->get('fiscal_year', '2081');

        $query = FinanceSale::with(['company', 'customer'])
            ->where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear);

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $sales = $query->latest('sale_date_bs')->paginate(20);

        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.sales.index', compact('sales', 'companies', 'companyId', 'fiscalYear'));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();
        $customers = FinanceCustomer::where('company_id', $companyId)->get();

        return view('admin.finance.sales.create', compact('companies', 'customers', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'sale_number' => 'required|string|unique:finance_sales,sale_number',
            'sale_date_bs' => 'required|string|max:10',
            'customer_id' => 'nullable|exists:finance_customers,id',
            'customer_name' => 'required|string|max:255',
            'invoice_number' => 'required|string',
            'taxable_amount' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid',
            'payment_method' => 'nullable|string',
            'payment_date_bs' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'fiscal_year_bs' => 'required|string|size:4',
        ]);

        $validated['created_by_user_id'] = Auth::id() ?? 1;

        FinanceSale::create($validated);

        return redirect()->route('admin.finance.sales.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Sale created successfully.');
    }

    public function show(FinanceSale $sale)
    {
        $sale->load(['company', 'customer']);
        return view('admin.finance.sales.show', compact('sale'));
    }

    public function edit(FinanceSale $sale)
    {
        $companies = FinanceCompany::where('is_active', true)->get();
        $customers = FinanceCustomer::where('company_id', $sale->company_id)->get();

        return view('admin.finance.sales.edit', compact('sale', 'companies', 'customers'));
    }

    public function update(Request $request, FinanceSale $sale)
    {
        $validated = $request->validate([
            'sale_date_bs' => 'required|string|max:10',
            'customer_id' => 'nullable|exists:finance_customers,id',
            'customer_name' => 'required|string|max:255',
            'taxable_amount' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid',
            'payment_method' => 'nullable|string',
            'payment_date_bs' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        $sale->update($validated);

        return redirect()->route('admin.finance.sales.index', ['company_id' => $sale->company_id])
            ->with('success', 'Sale updated successfully.');
    }

    public function destroy(FinanceSale $sale)
    {
        // Only allow deletion if payment status is pending
        if ($sale->payment_status !== 'pending') {
            return back()->with('error', 'Only pending sales can be deleted. Current status: ' . $sale->payment_status . '. Please contact administrator for paid/partial sales.');
        }

        $companyId = $sale->company_id;
        $sale->delete();

        return redirect()->route('admin.finance.sales.index', ['company_id' => $companyId])
            ->with('success', 'Sale deleted successfully.');
    }
}
