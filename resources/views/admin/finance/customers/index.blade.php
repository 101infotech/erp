@extends('admin.layouts.app')

@section('title', 'Finance Customers')
@section('page-title', 'Finance Customers')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Customers</h2>
        <div class="flex gap-2">
            <button onclick="toggleBulkActions()"
                class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-tasks mr-2"></i> Bulk Actions
            </button>
            <a href="{{ route('admin.finance.customers.create', ['company_id' => $companyId]) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> Add Customer
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar"
        class="hidden bg-blue-50 dark:bg-slate-700 border border-blue-200 dark:border-slate-600 rounded-lg p-4 mb-4">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">
                <span id="selectedCount">0</span> customer(s) selected
            </span>
            <div class="flex gap-2">
                <form action="{{ route('admin.finance.customers.export') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="customer_ids" id="exportIds">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-file-excel mr-2"></i> Export Selected
                    </button>
                </form>

                <form action="{{ route('admin.finance.customers.bulk-status') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="customer_ids" id="activateIds">
                    <input type="hidden" name="is_active" value="1">
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-check-circle mr-2"></i> Activate
                    </button>
                </form>

                <form action="{{ route('admin.finance.customers.bulk-status') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="customer_ids" id="deactivateIds">
                    <input type="hidden" name="is_active" value="0">
                    <button type="submit"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-ban mr-2"></i> Deactivate
                    </button>
                </form>

                <form action="{{ route('admin.finance.customers.bulk-delete') }}" method="POST" class="inline"
                    onsubmit="return confirm('Are you sure you want to delete the selected customers?')">
                    @csrf
                    <input type="hidden" name="customer_ids" id="deleteIds">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Company</label>
            <select name="company_id" onchange="this.form.submit()"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ $companyId==$company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Customer Type</label>
            <select name="customer_type" onchange="this.form.submit()"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                <option value="">All Types</option>
                <option value="individual" {{ request('customer_type')=='individual' ? 'selected' : '' }}>Individual
                </option>
                <option value="corporate" {{ request('customer_type')=='corporate' ? 'selected' : '' }}>Corporate
                </option>
                <option value="government" {{ request('customer_type')=='government' ? 'selected' : '' }}>Government
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
            <select name="is_active" onchange="this.form.submit()"
                class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                <option value="">All Status</option>
                <option value="1" {{ request('is_active')=='1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active')=='0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="flex items-end">
            <a href="{{ route('admin.finance.customers.index', ['company_id' => $companyId]) }}"
                class="w-full px-4 py-2 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-center">
                Clear Filters
            </a>
        </div>
    </form>

    <div class="w-full">
        <table class="w-full table-auto divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-700">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">PAN
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($customers as $customer)
                <tr>
                    <td class="px-6 py-4">
                        <input type="checkbox"
                            class="customer-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            value="{{ $customer->id }}" onchange="updateSelectedCount()">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                        {{ $customer->customer_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $customer->name }}</div>
                        @if($customer->contact_person)
                        <div class="text-xs text-slate-500">{{ $customer->contact_person }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($customer->customer_type === 'individual') bg-blue-100 text-blue-800
                            @elseif($customer->customer_type === 'corporate') bg-purple-100 text-purple-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($customer->customer_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                        {{ $customer->pan_number ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                        <div>{{ $customer->email ?? 'N/A' }}</div>
                        <div class="text-xs">{{ $customer->phone ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($customer->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.finance.customers.show', $customer) }}"
                            class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        <a href="{{ route('admin.finance.customers.edit', $customer) }}"
                            class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('admin.finance.customers.destroy', $customer) }}" method="POST"
                            class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">
                        No customers found. <a
                            href="{{ route('admin.finance.customers.create', ['company_id' => $companyId]) }}"
                            class="text-blue-600">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $customers->links() }}
    </div>
</div>

@push('scripts')
<script>
    function toggleBulkActions() {
        const bar = document.getElementById('bulkActionsBar');
        bar.classList.toggle('hidden');
        if (!bar.classList.contains('hidden')) {
            updateSelectedCount();
        }
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.customer-checkbox:checked');
        const count = checkboxes.length;
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('exportIds').value = JSON.stringify(ids);
        document.getElementById('activateIds').value = JSON.stringify(ids);
        document.getElementById('deactivateIds').value = JSON.stringify(ids);
        document.getElementById('deleteIds').value = JSON.stringify(ids);

        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.customer-checkbox');
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
    }
</script>
@endpush

@endsection