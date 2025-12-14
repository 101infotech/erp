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
        if ($founder->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete founder with existing transactions.');
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
        // TODO: Implement export functionality
        return back()->with('info', 'Export feature coming soon.');
    }
}
