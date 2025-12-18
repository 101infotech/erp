<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">New Resource Request</h1>
                    <p class="text-slate-400">Tell us what you need</p>
                </div>
                <a href="{{ route('employee.resource-requests.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">Cancel</a>
            </div>

            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                <form method="POST" action="{{ route('employee.resource-requests.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Item name</label>
                        <input type="text" name="item_name" value="{{ old('item_name') }}" required
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Quantity</label>
                            <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Priority</label>
                            <select name="priority"
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500"
                                required>
                                <option value="low" {{ old('priority')==='low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority','medium')==='medium' ? 'selected' : '' }}>
                                    Medium</option>
                                <option value="high" {{ old('priority')==='high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority')==='urgent' ? 'selected' : '' }}>Urgent
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Category</label>
                            <select name="category"
                                class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500"
                                required>
                                <option value="office_supplies" {{ old('category')==='office_supplies' ? 'selected' : ''
                                    }}>Office supplies</option>
                                <option value="equipment" {{ old('category')==='equipment' ? 'selected' : '' }}>
                                    Equipment</option>
                                <option value="pantry" {{ old('category')==='pantry' ? 'selected' : '' }}>Pantry
                                </option>
                                <option value="furniture" {{ old('category')==='furniture' ? 'selected' : '' }}>
                                    Furniture</option>
                                <option value="technology" {{ old('category')==='technology' ? 'selected' : '' }}>
                                    Technology</option>
                                <option value="other" {{ old('category')==='other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Reason / details</label>
                        <textarea name="reason" rows="3"
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">{{ old('reason') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Description (optional)</label>
                        <textarea name="description" rows="2"
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Estimated cost (optional)</label>
                        <input type="number" step="0.01" min="0" name="estimated_cost"
                            value="{{ old('estimated_cost') }}"
                            class="w-full bg-slate-700 border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-lime-500 focus:border-lime-500">
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="px-5 py-2 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400">Submit</button>
                        <a href="{{ route('employee.resource-requests.index') }}"
                            class="text-slate-300 hover:text-white">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>