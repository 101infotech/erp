<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceAsset;
use App\Models\FinanceAssetDepreciation;
use App\Models\FinanceCompany;
use App\Models\FinanceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceAsset::with(['company', 'category']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('asset_name', 'like', '%' . $request->search . '%')
                    ->orWhere('asset_number', 'like', '%' . $request->search . '%')
                    ->orWhere('serial_number', 'like', '%' . $request->search . '%');
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        $companies = FinanceCompany::active()->get();

        return view('admin.finance.assets.index', compact('assets', 'companies'));
    }

    public function create()
    {
        $companies = FinanceCompany::active()->get();
        $categories = FinanceCategory::where('type', 'asset')->where('is_active', true)->get();

        return view('admin.finance.assets.create', compact('companies', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'category_id' => 'nullable|exists:finance_categories,id',
            'asset_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'asset_type' => 'required|in:tangible,intangible',
            'asset_category' => 'required|string|max:100',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date_bs' => 'required|string',
            'fiscal_year_bs' => 'required|string',
            'vendor_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:100',
            'depreciation_method' => 'required|in:straight_line,declining_balance,sum_of_years,units_of_production,none',
            'useful_life_years' => 'nullable|integer|min:1',
            'useful_life_months' => 'nullable|integer|min:0|max:11',
            'salvage_value' => 'nullable|numeric|min:0',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'location' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:100',
        ]);

        // Generate asset number
        $year = $request->fiscal_year_bs;
        $lastAsset = FinanceAsset::where('fiscal_year_bs', $year)->latest('id')->first();
        $sequence = $lastAsset ? ((int) substr($lastAsset->asset_number, -6)) + 1 : 1;
        $assetNumber = sprintf("AST-%s-%06d", $year, $sequence);

        $validated['asset_number'] = $assetNumber;
        $validated['book_value'] = $request->purchase_cost;
        $validated['accumulated_depreciation'] = 0;
        $validated['status'] = 'active';
        $validated['created_by'] = Auth::id();

        FinanceAsset::create($validated);

        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset created successfully.');
    }

    public function show(FinanceAsset $asset)
    {
        $asset->load(['company', 'category', 'depreciationRecords']);
        return view('admin.finance.assets.show', compact('asset'));
    }

    public function edit(FinanceAsset $asset)
    {
        $companies = FinanceCompany::active()->get();
        $categories = FinanceCategory::where('type', 'asset')->where('is_active', true)->get();

        return view('admin.finance.assets.edit', compact('asset', 'companies', 'categories'));
    }

    public function update(Request $request, FinanceAsset $asset)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:finance_companies,id',
            'category_id' => 'nullable|exists:finance_categories,id',
            'asset_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'status' => 'required|in:active,disposed,sold,transferred,under_maintenance,written_off',
        ]);

        $validated['updated_by'] = Auth::id();
        $asset->update($validated);

        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(FinanceAsset $asset)
    {
        $asset->delete();
        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    public function calculateDepreciation(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:finance_assets,id',
            'fiscal_year_bs' => 'required|string',
            'fiscal_month_bs' => 'required|string',
        ]);

        $asset = FinanceAsset::findOrFail($request->asset_id);

        // Check for existing record
        $existing = FinanceAssetDepreciation::where('finance_asset_id', $asset->id)
            ->where('fiscal_year_bs', $request->fiscal_year_bs)
            ->where('fiscal_month_bs', $request->fiscal_month_bs)
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors(['error' => 'Depreciation already calculated for this period.']);
        }

        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();
        $newAccumulated = $asset->accumulated_depreciation + $monthlyDepreciation;
        $newBookValue = $asset->purchase_cost - $newAccumulated;

        DB::transaction(function () use ($asset, $request, $monthlyDepreciation, $newAccumulated, $newBookValue) {
            FinanceAssetDepreciation::create([
                'finance_asset_id' => $asset->id,
                'company_id' => $asset->company_id,
                'fiscal_year_bs' => $request->fiscal_year_bs,
                'fiscal_month_bs' => $request->fiscal_month_bs,
                'depreciation_date_bs' => now()->format('Y-m-d'),
                'opening_book_value' => $asset->book_value,
                'depreciation_amount' => $monthlyDepreciation,
                'accumulated_depreciation' => $newAccumulated,
                'closing_book_value' => max($newBookValue, $asset->salvage_value ?? 0),
                'calculation_method' => $asset->depreciation_method,
                'status' => 'posted',
                'posted_by' => Auth::id(),
                'posted_date_bs' => now()->format('Y-m-d'),
            ]);

            $asset->update([
                'accumulated_depreciation' => $newAccumulated,
                'book_value' => max($newBookValue, $asset->salvage_value ?? 0),
            ]);
        });

        return redirect()->back()->with('success', 'Depreciation calculated and recorded successfully.');
    }

    public function dispose(Request $request, FinanceAsset $asset)
    {
        $validated = $request->validate([
            'disposal_date_bs' => 'required|string',
            'disposal_value' => 'nullable|numeric|min:0',
            'disposal_reason' => 'required|string',
        ]);

        $asset->update([
            'status' => 'disposed',
            'disposal_date_bs' => $request->disposal_date_bs,
            'disposal_value' => $request->disposal_value,
            'disposal_reason' => $request->disposal_reason,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset disposed successfully.');
    }

    public function transfer(Request $request, FinanceAsset $asset)
    {
        $validated = $request->validate([
            'to_company_id' => 'required|exists:finance_companies,id|different:company_id',
            'transfer_date_bs' => 'required|string',
            'transfer_notes' => 'nullable|string',
        ]);

        $asset->update([
            'transferred_to_company_id' => $request->to_company_id,
            'transfer_date_bs' => $request->transfer_date_bs,
            'transfer_notes' => $request->transfer_notes,
            'status' => 'transferred',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.finance.assets.index')
            ->with('success', 'Asset transferred successfully.');
    }
}
