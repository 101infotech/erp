<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancePurchase;
use App\Models\FinanceCompany;
use App\Models\FinanceVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancePurchaseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $fiscalYear = $request->get('fiscal_year', '2081');

        $query = FinancePurchase::with(['company', 'vendor'])
            ->where('company_id', $companyId)
            ->where('fiscal_year_bs', $fiscalYear);

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->latest('purchase_date_bs')->paginate(20);

        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.purchases.index', compact('purchases', 'companies', 'companyId', 'fiscalYear'));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();
        $vendors = FinanceVendor::where('company_id', $companyId)->get();

        return view('admin.finance.purchases.create', compact('companies', 'vendors', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'purchase_number' => 'required|string|unique:finance_purchases,purchase_number',
            'purchase_date_bs' => 'required|string|max:10',
            'vendor_id' => 'nullable|exists:finance_vendors,id',
            'vendor_name' => 'required|string|max:255',
            'bill_number' => 'required|string',
            'taxable_amount' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'tds_amount' => 'nullable|numeric|min:0',
            'tds_percentage' => 'nullable|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid',
            'payment_method' => 'nullable|string',
            'payment_date_bs' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'fiscal_year_bs' => 'required|string|size:4',
        ]);

        $validated['created_by_user_id'] = Auth::id() ?? 1;

        FinancePurchase::create($validated);

        return redirect()->route('admin.finance.purchases.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Purchase created successfully.');
    }

    public function show(FinancePurchase $purchase)
    {
        $purchase->load(['company', 'vendor']);
        return view('admin.finance.purchases.show', compact('purchase'));
    }

    public function edit(FinancePurchase $purchase)
    {
        $companies = FinanceCompany::where('is_active', true)->get();
        $vendors = FinanceVendor::where('company_id', $purchase->company_id)->get();

        return view('admin.finance.purchases.edit', compact('purchase', 'companies', 'vendors'));
    }

    public function update(Request $request, FinancePurchase $purchase)
    {
        $validated = $request->validate([
            'purchase_date_bs' => 'required|string|max:10',
            'vendor_id' => 'nullable|exists:finance_vendors,id',
            'vendor_name' => 'required|string|max:255',
            'taxable_amount' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'tds_amount' => 'nullable|numeric|min:0',
            'tds_percentage' => 'nullable|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid',
            'payment_method' => 'nullable|string',
            'payment_date_bs' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        $purchase->update($validated);

        return redirect()->route('admin.finance.purchases.index', ['company_id' => $purchase->company_id])
            ->with('success', 'Purchase updated successfully.');
    }

    public function destroy(FinancePurchase $purchase)
    {
        // Only allow deletion if payment status is pending
        if ($purchase->payment_status !== 'pending') {
            return back()->with('error', 'Only pending purchases can be deleted. Current status: ' . $purchase->payment_status . '. Please contact administrator for paid/partial purchases.');
        }

        $companyId = $purchase->company_id;
        $purchase->delete();

        return redirect()->route('admin.finance.purchases.index', ['company_id' => $companyId])
            ->with('success', 'Purchase deleted successfully.');
    }
}
