<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceFounder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceFounderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FinanceFounder::query()->with('transactions');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('pan_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $founders = $query->latest()->paginate(20);

        // Calculate balances for each founder
        foreach ($founders as $founder) {
            $founder->total_investment = $founder->getTotalInvestment();
            $founder->total_withdrawal = $founder->getTotalWithdrawal();
            $founder->net_balance = $founder->getNetBalance();
        }

        return view('admin.finance.founders.index', compact('founders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.finance.founders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:50',
            'citizenship_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'joined_date_bs' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $founder = FinanceFounder::create($validated);

        return redirect()
            ->route('admin.finance.founders.show', $founder)
            ->with('success', 'Founder created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FinanceFounder $founder)
    {
        $founder->load(['transactions' => function ($query) {
            $query->with(['company', 'createdBy'])->latest();
        }]);

        // Calculate statistics
        $stats = [
            'total_investment' => $founder->getTotalInvestment(),
            'total_withdrawal' => $founder->getTotalWithdrawal(),
            'net_balance' => $founder->getNetBalance(),
            'total_transactions' => $founder->transactions->count(),
            'pending_transactions' => $founder->transactions->where('status', 'pending')->count(),
            'approved_transactions' => $founder->transactions->where('status', 'approved')->count(),
        ];

        // Get transactions by company
        $transactionsByCompany = $founder->transactions->groupBy('company_id')->map(function ($transactions) {
            return [
                'company' => $transactions->first()->company,
                'investment' => $transactions->where('transaction_type', 'investment')->sum('amount'),
                'withdrawal' => $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
                'net' => $transactions->where('transaction_type', 'investment')->sum('amount') -
                    $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
            ];
        });

        return view('admin.finance.founders.show', compact('founder', 'stats', 'transactionsByCompany'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinanceFounder $founder)
    {
        return view('admin.finance.founders.edit', compact('founder'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinanceFounder $founder)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:50',
            'citizenship_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'joined_date_bs' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $founder->update($validated);

        return redirect()
            ->route('admin.finance.founders.show', $founder)
            ->with('success', 'Founder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinanceFounder $founder)
    {
        // Check if founder has transactions
        $transactionsCount = $founder->transactions()->count();

        if ($transactionsCount > 0) {
            return back()->with('error', "Cannot delete founder with {$transactionsCount} existing transaction(s). Deactivate the founder instead.");
        }

        // Check if founder has any outstanding balance
        if ($founder->current_balance != 0) {
            return back()->with('error', 'Cannot delete founder with non-zero balance (NPR ' . number_format($founder->current_balance, 2) . '). Please settle all balances first.');
        }

        $founder->delete();

        return redirect()
            ->route('admin.finance.founders.index')
            ->with('success', 'Founder deleted successfully.');
    }

    /**
     * Export founders list
     */
    public function export(Request $request)
    {
        $query = FinanceFounder::query()->with('transactions');

        // Apply same filters as index page
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('pan_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $founders = $query->latest()->get();

        // Prepare export data
        $exportData = [];
        $exportData[] = [
            'Name',
            'Email',
            'Phone',
            'PAN Number',
            'Citizenship Number',
            'Address',
            'Ownership %',
            'Joined Date (BS)',
            'Total Investment',
            'Total Withdrawal',
            'Net Balance',
            'Status',
            'Created At',
        ];

        foreach ($founders as $founder) {
            $exportData[] = [
                $founder->name,
                $founder->email ?? 'N/A',
                $founder->phone ?? 'N/A',
                $founder->pan_number ?? 'N/A',
                $founder->citizenship_number ?? 'N/A',
                $founder->address ?? 'N/A',
                $founder->ownership_percentage ?? 'N/A',
                $founder->joined_date_bs ?? 'N/A',
                number_format($founder->getTotalInvestment(), 2),
                number_format($founder->getTotalWithdrawal(), 2),
                number_format($founder->getNetBalance(), 2),
                $founder->is_active ? 'Active' : 'Inactive',
                $founder->created_at->format('Y-m-d H:i:s'),
            ];
        }

        // Generate CSV
        $filename = 'founders_export_' . date('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Create exports directory if it doesn't exist
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $file = fopen($filepath, 'w');
        foreach ($exportData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }
}
