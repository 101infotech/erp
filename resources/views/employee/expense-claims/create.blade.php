<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">New Expense Claim</h1>
                    <p class="text-slate-400">Submit your expense for reimbursement</p>
                </div>
                <a href="{{ route('employee.expense-claims.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">Cancel</a>
            </div>

            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                <form method="POST" action="{{ route('employee.expense-claims.store') }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Expense Type</label>
                            <select name="expense_type"
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500"
                                required>
                                <option value="travel" {{ old('expense_type')==='travel' ? 'selected' : '' }}>Travel
                                </option>
                                <option value="accommodation" {{ old('expense_type')==='accommodation' ? 'selected' : ''
                                    }}>Accommodation</option>
                                <option value="meals" {{ old('expense_type')==='meals' ? 'selected' : '' }}>Meals
                                </option>
                                <option value="transportation" {{ old('expense_type')==='transportation' ? 'selected'
                                    : '' }}>Transportation</option>
                                <option value="supplies" {{ old('expense_type')==='supplies' ? 'selected' : '' }}>
                                    Supplies</option>
                                <option value="communication" {{ old('expense_type')==='communication' ? 'selected' : ''
                                    }}>Communication</option>
                                <option value="other" {{ old('expense_type')==='other' ? 'selected' : '' }}>Other
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" required
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Currency</label>
                            <input type="text" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3"
                                required
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Expense Date</label>
                            <input type="date" name="expense_date" value="{{ old('expense_date') }}" required
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Receipt (PDF or Image, max
                            5MB)</label>
                        <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                        <p class="text-xs text-slate-400 mt-1">Attach proof of expense</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="px-5 py-2 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400">Submit
                            Claim</button>
                        <a href="{{ route('employee.expense-claims.index') }}"
                            class="text-slate-300 hover:text-white">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>