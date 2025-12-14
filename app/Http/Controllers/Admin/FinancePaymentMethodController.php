<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancePaymentMethod;
use Illuminate\Http\Request;

class FinancePaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancePaymentMethod::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $paymentMethods = $query->orderBy('name')->paginate(20);

        return view('admin.finance.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.finance.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:finance_payment_methods,name',
            'code' => 'nullable|string|max:20|unique:finance_payment_methods,code',
            'description' => 'nullable|string',
            'requires_reference' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['requires_reference'] = $request->has('requires_reference');
        $validated['is_active'] = $request->has('is_active');

        FinancePaymentMethod::create($validated);

        return redirect()->route('admin.finance.payment-methods.index')
            ->with('success', 'Payment method created successfully.');
    }

    public function edit(FinancePaymentMethod $paymentMethod)
    {
        return view('admin.finance.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, FinancePaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:finance_payment_methods,name,' . $paymentMethod->id,
            'code' => 'nullable|string|max:20|unique:finance_payment_methods,code,' . $paymentMethod->id,
            'description' => 'nullable|string',
            'requires_reference' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['requires_reference'] = $request->has('requires_reference');
        $validated['is_active'] = $request->has('is_active');

        $paymentMethod->update($validated);

        return redirect()->route('admin.finance.payment-methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(FinancePaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('admin.finance.payment-methods.index')
            ->with('success', 'Payment method deleted successfully.');
    }
}
