@extends('admin.layouts.app')

@section('title', 'Resource Requests')
@section('page-title', 'Resource Requests')

@section('content')
<div class="px-6 md:px-8 py-6 space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Resource Requests</h1>
            <p class="text-slate-400 mt-1">Manage staff resource and item requests</p>
        </div>
        <a href="{{ route('admin.hrm.resource-requests.create') }}"
            class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
            New Request
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Pending</div>
            <div class="text-2xl font-bold text-yellow-500">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Approved</div>
            <div class="text-2xl font-bold text-blue-500">{{ $stats['approved'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Fulfilled</div>
            <div class="text-2xl font-bold text-green-500">{{ $stats['fulfilled'] }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">Rejected</div>
            <div class="text-2xl font-bold text-red-500">{{ $stats['rejected'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 md:p-6 border border-slate-200 dark:border-slate-700">
        <form method="GET" action="{{ route('admin.hrm.resource-requests.index') }}"
            class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                <select name="status"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
                    <option value="fulfilled" {{ request('status')==='fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                    <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-300 mb-2">Category</label>
                <select name="category"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500">
                    <option value="">All Categories</option>
                    <option value="office_supplies" {{ request('category')==='office_supplies' ? 'selected' : '' }}>
                        Office Supplies</option>
                    <option value="equipment" {{ request('category')==='equipment' ? 'selected' : '' }}>Equipment
                    </option>
                    <option value="pantry" {{ request('category')==='pantry' ? 'selected' : '' }}>Pantry</option>
                    <option value="furniture" {{ request('category')==='furniture' ? 'selected' : '' }}>Furniture
                    </option>
                    <option value="technology" {{ request('category')==='technology' ? 'selected' : '' }}>Technology
                    </option>
                    <option value="other" {{ request('category')==='other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-300 mb-2">Priority</label>
                <select name="priority"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-lime-500">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority')==='low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority')==='medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority')==='high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority')==='urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            <button type="submit"
                class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 font-semibold rounded-lg transition">
                Filter
            </button>
            <a href="{{ route('admin.hrm.resource-requests.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                Reset
            </a>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900 border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Employee</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Item</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Quantity</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Category</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Priority</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($requests as $request)
                    <tr class="hover:bg-slate-700/50">
                        <td class="px-4 py-3 text-sm text-slate-300">#{{ $request->id }}</td>
                        <td class="px-4 py-3 text-sm text-white">{{ $request->employee->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-white">{{ $request->item_name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $request->quantity }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">
                            <span class="capitalize">{{ str_replace('_', ' ', $request->category) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($request->priority === 'urgent') bg-red-500/20 text-red-400
                                @elseif($request->priority === 'high') bg-yellow-500/20 text-yellow-400
                                @elseif($request->priority === 'medium') bg-blue-500/20 text-blue-400
                                @else bg-slate-500/20 text-slate-400
                                @endif">
                                {{ ucfirst($request->priority) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($request->status === 'fulfilled') bg-green-500/20 text-green-400
                                @elseif($request->status === 'approved') bg-blue-500/20 text-blue-400
                                @elseif($request->status === 'pending') bg-yellow-500/20 text-yellow-400
                                @else bg-red-500/20 text-red-400
                                @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $request->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('admin.hrm.resource-requests.show', $request->id) }}"
                                class="text-lime-400 hover:text-lime-300">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                            No resource requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
        <div class="px-4 py-3 border-t border-slate-700">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection