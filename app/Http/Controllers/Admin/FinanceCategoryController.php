<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;

class FinanceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceCategory::query()->with(['company', 'parentCategory', 'subCategories']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->orderBy('name')->paginate(20);
        $companies = FinanceCompany::active()->orderBy('name')->get();

        return view('admin.finance.categories.index', compact('categories', 'companies'));
    }

    public function create()
    {
        $companies = FinanceCompany::active()->orderBy('name')->get();
        $parentCategories = FinanceCategory::whereNull('parent_category_id')->orderBy('name')->get();
        return view('admin.finance.categories.create', compact('companies', 'parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:finance_companies,id',
            'parent_category_id' => 'nullable|exists:finance_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:income,expense,asset,liability,equity',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        FinanceCategory::create($validated);

        return redirect()->route('admin.finance.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(FinanceCategory $category)
    {
        $companies = FinanceCompany::active()->orderBy('name')->get();
        $parentCategories = FinanceCategory::whereNull('parent_category_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')->get();
        return view('admin.finance.categories.edit', compact('category', 'companies', 'parentCategories'));
    }

    public function update(Request $request, FinanceCategory $category)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:finance_companies,id',
            'parent_category_id' => 'nullable|exists:finance_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:income,expense,asset,liability,equity',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $category->update($validated);

        return redirect()->route('admin.finance.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(FinanceCategory $category)
    {
        if ($category->subCategories()->count() > 0) {
            return back()->with('error', 'Cannot delete category with subcategories.');
        }

        $category->delete();
        return redirect()->route('admin.finance.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
