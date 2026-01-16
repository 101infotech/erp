@extends('admin.layouts.app')

@section('content')
<div class="px-6 md:px-8 py-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Leave Policies</h1>
            <p class="text-slate-400 mt-1">Manage leave policies for different companies and leave types</p>
        </div>
        <a href="{{ route('admin.hrm.leave-policies.create') }}"
            class="mt-4 sm:mt-0 px-4 py-2 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition flex items-center space-x-2 w-fit">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Policy</span>
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-500/10 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Policies Table -->
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Policy Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Leave Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Annual Quota</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Carry Forward</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($policies as $policy)
                    <tr class="hover:bg-slate-750">
                        <td class="px-6 py-4 text-sm text-white">{{ $policy->company->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-300">{{ $policy->policy_name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($policy->leave_type === 'annual') bg-blue-500/20 text-blue-400
                                @elseif($policy->leave_type === 'sick') bg-red-500/20 text-red-400
                                @elseif($policy->leave_type === 'casual') bg-green-500/20 text-green-400
                                @else bg-slate-500/20 text-slate-400
                                @endif">
                                {{ ucfirst($policy->leave_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium text-white">
                            {{ $policy->annual_quota }} days
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-slate-300">
                            @if($policy->carry_forward_allowed)
                            <span class="text-green-400">✓ Up to {{ $policy->max_carry_forward }} days</span>
                            @else
                            <span class="text-slate-500">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($policy->is_active)
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">Active</span>
                            @else
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-500/20 text-slate-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.hrm.leave-policies.edit', $policy) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-500/10 text-blue-400 rounded-lg hover:bg-blue-500/20 transition"
                                    title="Edit Policy">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.hrm.leave-policies.destroy', $policy) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this leave policy?')"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-500/10 text-red-400 rounded-lg hover:bg-red-500/20 transition"
                                        title="Delete Policy">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-16 h-16 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium">No leave policies found</p>
                            <p class="mt-1">Get started by creating your first leave policy.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($policies->hasPages())
        <div class="px-6 py-4 border-t border-slate-700">
            {{ $policies->links() }}
        </div>
        @endif
    </div>
</div>
@endsection