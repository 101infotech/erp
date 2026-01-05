@extends('admin.layouts.app')

@section('title', 'Create Budget - Finance Management')

@section('content')
<div class="container-fluid px-4 py-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.finance.budgets.index') }}"
                class="hover:text-gray-900 dark:hover:text-white">Budgets</a>
            <span>/</span>
            <span>Create New Budget</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Budget</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Set up budget allocation for monitoring expenses</p>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.finance.budgets.store') }}" method="POST"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        @csrf

        <div class="p-6 space-y-6">
            <!-- Company Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Company <span class="text-red-500">*</span>
                </label>
                <select name="company_id" id="company_id" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('company_id') border-red-500 @enderror">
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $companyId)==$company->id ? 'selected' : ''
                        }}>
                        {{ $company->company_name }}
                    </option>
                    @endforeach
                </select>
                @error('company_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fiscal Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Fiscal Year (BS) <span class="text-red-500">*</span>
                </label>
                <input type="text" name="fiscal_year_bs" id="fiscal_year_bs" maxlength="4" required
                    value="{{ old('fiscal_year_bs', '2081') }}" placeholder="e.g., 2081"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('fiscal_year_bs') border-red-500 @enderror">
                @error('fiscal_year_bs')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Expense Category
                </label>
                <select name="category_id" id="category_id"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                    <option value="">Select Category (Optional)</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank for overall budget tracking</p>
            </div>

            <!-- Budget Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Budget Type <span class="text-red-500">*</span>
                </label>
                <select name="budget_type" id="budget_type" required onchange="updatePeriodOptions()"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('budget_type') border-red-500 @enderror">
                    <option value="">Select Budget Type</option>
                    <option value="monthly" {{ old('budget_type')=='monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ old('budget_type')=='quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="annual" {{ old('budget_type')=='annual' ? 'selected' : '' }}>Annual</option>
                </select>
                @error('budget_type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Period -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Period <span class="text-red-500">*</span>
                </label>
                <select name="period" id="period" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('period') border-red-500 @enderror">
                    <option value="">Select Period</option>
                </select>
                @error('period')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="period_help">Select budget type first</p>
            </div>

            <!-- Budgeted Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Budgeted Amount (NPR) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="budgeted_amount" id="budgeted_amount" step="0.01" min="0" required
                    value="{{ old('budgeted_amount') }}" placeholder="0.00"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('budgeted_amount') border-red-500 @enderror">
                @error('budgeted_amount')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="draft" {{ old('status', 'draft' )=='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ old('status')=='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
                </select>
                @error('status')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Additional notes or comments..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div
            class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600 rounded-b-lg flex gap-3">
            <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                Create Budget
            </button>
            <a href="{{ route('admin.finance.budgets.index') }}"
                class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    function updatePeriodOptions() {
    const budgetType = document.getElementById('budget_type').value;
    const periodSelect = document.getElementById('period');
    const periodHelp = document.getElementById('period_help');
    
    // Clear existing options
    periodSelect.innerHTML = '<option value="">Select Period</option>';
    
    if (budgetType === 'monthly') {
        for (let i = 1; i <= 12; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `Month ${i}`;
            periodSelect.appendChild(option);
        }
        periodHelp.textContent = 'Select month (1-12)';
    } else if (budgetType === 'quarterly') {
        for (let i = 1; i <= 4; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `Quarter ${i}`;
            periodSelect.appendChild(option);
        }
        periodHelp.textContent = 'Select quarter (1-4)';
    } else if (budgetType === 'annual') {
        const option = document.createElement('option');
        option.value = 1;
        option.textContent = 'Full Year';
        periodSelect.appendChild(option);
        periodSelect.value = 1;
        periodHelp.textContent = 'Full fiscal year budget';
    } else {
        periodHelp.textContent = 'Select budget type first';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const budgetType = document.getElementById('budget_type').value;
    if (budgetType) {
        updatePeriodOptions();
        const oldPeriod = "{{ old('period') }}";
        if (oldPeriod) {
            document.getElementById('period').value = oldPeriod;
        }
    }
});
</script>
@endsection