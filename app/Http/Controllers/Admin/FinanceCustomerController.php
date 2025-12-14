<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceCustomer;
use App\Models\FinanceCompany;
use App\Exports\CustomersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinanceCustomerController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->get('company_id', 1);

        $query = FinanceCustomer::with('company')
            ->where('company_id', $companyId);

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $customers = $query->latest()->paginate(20);
        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.customers.index', compact('customers', 'companies', 'companyId'));
    }

    public function create(Request $request)
    {
        $companyId = $request->get('company_id', 1);
        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.customers.create', compact('companies', 'companyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'customer_code' => 'nullable|string|max:20|unique:finance_customers,customer_code',
            'name' => 'required|string|max:255',
            'pan_number' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'customer_type' => 'required|in:individual,corporate,government',
            'is_active' => 'boolean',
        ]);

        // Auto-generate customer code if not provided
        if (empty($validated['customer_code'])) {
            $validated['customer_code'] = 'CUST-' . str_pad(FinanceCustomer::count() + 1, 5, '0', STR_PAD_LEFT);
        }

        FinanceCustomer::create($validated);

        return redirect()->route('admin.finance.customers.index', ['company_id' => $validated['company_id']])
            ->with('success', 'Customer created successfully.');
    }

    public function show(FinanceCustomer $customer)
    {
        $customer->load(['company', 'sales', 'documents.uploader']);

        // Calculate total sales and outstanding
        $totalSales = $customer->sales()->sum('total_amount');
        $paidSales = $customer->sales()->where('payment_status', 'paid')->sum('total_amount');
        $outstanding = $totalSales - $paidSales;

        return view('admin.finance.customers.show', compact('customer', 'totalSales', 'paidSales', 'outstanding'));
    }

    public function edit(FinanceCustomer $customer)
    {
        $companies = FinanceCompany::where('is_active', true)->get();

        return view('admin.finance.customers.edit', compact('customer', 'companies'));
    }

    public function update(Request $request, FinanceCustomer $customer)
    {
        $validated = $request->validate([
            'customer_code' => 'nullable|string|max:20|unique:finance_customers,customer_code,' . $customer->id,
            'name' => 'required|string|max:255',
            'pan_number' => 'nullable|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'customer_type' => 'required|in:individual,corporate,government',
            'is_active' => 'boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.finance.customers.index', ['company_id' => $customer->company_id])
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(FinanceCustomer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.finance.customers.index', ['company_id' => $customer->company_id])
            ->with('success', 'Customer deleted successfully.');
    }

    public function export(Request $request)
    {
        $customerIds = $request->input('customer_ids');
        $fileName = 'customers_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new CustomersExport($customerIds), $fileName);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:finance_customers,id',
            'is_active' => 'required|boolean',
        ]);

        FinanceCustomer::whereIn('id', $request->customer_ids)
            ->update(['is_active' => $request->is_active]);

        $status = $request->is_active ? 'activated' : 'deactivated';
        $count = count($request->customer_ids);

        return redirect()->back()->with('success', "{$count} customer(s) {$status} successfully.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:finance_customers,id',
        ]);

        $count = count($request->customer_ids);
        FinanceCustomer::whereIn('id', $request->customer_ids)->delete();

        return redirect()->back()->with('success', "{$count} customer(s) deleted successfully.");
    }
}
