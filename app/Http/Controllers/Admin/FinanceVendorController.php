<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceVendor;
use App\Models\FinanceCompany;
use App\Exports\VendorsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinanceVendorController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);

        $query = FinanceVendor::with('company')
            ->where('company_id', $companyId);

        if ($request->filled('vendor_type')) {
            $query->where('vendor_type', $request->vendor_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $vendors = $query->latest()->paginate(20);
        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.vendors.index', compact('vendors', 'companies', 'companyId'));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.vendors.create', compact('companies', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'vendor_code' => 'nullable|string|max:20|unique:finance_vendors,vendor_code',
            'name' => 'required|string|max:255',
            'pan_number' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'vendor_type' => 'required|in:supplier,contractor,service_provider',
            'is_active' => 'boolean',
        ]);

        // Auto-generate vendor code if not provided
        if (empty($validated['vendor_code'])) {
            $validated['vendor_code'] = 'VEND-' . str_pad(FinanceVendor::count() + 1, 5, '0', STR_PAD_LEFT);
        }

        FinanceVendor::create($validated);

        return redirect()->route('admin.finance.vendors.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Vendor created successfully.');
    }

    public function show(FinanceVendor $vendor)
    {
        $vendor->load(['company', 'purchases', 'documents.uploader']);

        // Calculate total purchases and outstanding
        $totalPurchases = $vendor->purchases()->sum('total_amount');
        $paidPurchases = $vendor->purchases()->where('payment_status', 'paid')->sum('total_amount');
        $outstanding = $totalPurchases - $paidPurchases;

        return view('admin.finance.vendors.show', compact('vendor', 'totalPurchases', 'paidPurchases', 'outstanding'));
    }

    public function edit(FinanceVendor $vendor)
    {
        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.vendors.edit', compact('vendor', 'companies'));
    }

    public function update(Request $request, FinanceVendor $vendor)
    {
        $validated = $request->validate([
            'vendor_code' => 'nullable|string|max:20|unique:finance_vendors,vendor_code,' . $vendor->id,
            'name' => 'required|string|max:255',
            'pan_number' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'vendor_type' => 'required|in:supplier,contractor,service_provider',
            'is_active' => 'boolean',
        ]);

        $vendor->update($validated);

        return redirect()->route('admin.finance.vendors.index', ['company_id' => $vendor->company_id])
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy(FinanceVendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('admin.finance.vendors.index', ['company_id' => $vendor->company_id])
            ->with('success', 'Vendor deleted successfully.');
    }

    public function export(Request $request)
    {
        $vendorIds = $request->input('vendor_ids');
        $fileName = 'vendors_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new VendorsExport($vendorIds), $fileName);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'vendor_ids' => 'required|array',
            'vendor_ids.*' => 'exists:finance_vendors,id',
            'is_active' => 'required|boolean',
        ]);

        FinanceVendor::whereIn('id', $request->vendor_ids)
            ->update(['is_active' => $request->is_active]);

        $status = $request->is_active ? 'activated' : 'deactivated';
        $count = count($request->vendor_ids);

        return redirect()->back()->with('success', "{$count} vendor(s) {$status} successfully.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'vendor_ids' => 'required|array',
            'vendor_ids.*' => 'exists:finance_vendors,id',
        ]);

        $count = count($request->vendor_ids);
        FinanceVendor::whereIn('id', $request->vendor_ids)->delete();

        return redirect()->back()->with('success', "{$count} vendor(s) deleted successfully.");
    }
}
