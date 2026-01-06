@extends('admin.layouts.app')

@section('title', 'Holidays')
@section('page-title', 'Company Holidays')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-white">Holidays</h2>
            <p class="text-slate-400 mt-1">Manage company holidays that appear on attendance calendars</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 lg:col-span-1">
            <h3 class="text-lg font-semibold text-white mb-4">{{ isset($holiday) ? 'Edit Holiday' : 'Add Holiday' }}
            </h3>
            <form method="POST"
                action="{{ isset($holiday) ? route('admin.hrm.holidays.update', $holiday) : route('admin.hrm.holidays.store') }}"
                class="space-y-4">
                @csrf
                @if(isset($holiday))
                @method('PUT')
                @endif

                <div>
                    <label class="block text-sm text-slate-300 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $holiday->name ?? '') }}"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-1">Date</label>
                    <input type="date" name="date"
                        value="{{ old('date', isset($holiday) ? $holiday->date->format('Y-m-d') : '') }}"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm text-slate-300 mb-1">Description (optional)</label>
                    <input type="text" name="description" value="{{ old('description', $holiday->description ?? '') }}"
                        class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500"
                        maxlength="500">
                </div>

                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center gap-2 text-slate-300">
                        <input type="checkbox" name="is_company_wide" value="1"
                            class="rounded border-slate-600 bg-slate-700 text-lime-500" {{ old('is_company_wide',
                            $holiday->is_company_wide ?? true) ? 'checked' : '' }}>
                        <span>Company-wide</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-slate-300">
                        <input type="checkbox" name="is_active" value="1"
                            class="rounded border-slate-600 bg-slate-700 text-lime-500" {{ old('is_active',
                            $holiday->is_active ?? true) ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-lime-500 text-slate-950 font-semibold rounded-lg hover:bg-lime-400">{{
                        isset($holiday) ? 'Update' : 'Add' }}</button>
                    @if(isset($holiday))
                    <a href="{{ route('admin.hrm.holidays.index') }}"
                        class="px-3 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">Cancel</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Holiday List</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-900">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Company-wide</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Active</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        @forelse($holidays as $item)
                        <tr class="hover:bg-slate-700/40 transition">
                            <td class="px-4 py-3 text-sm text-slate-200 whitespace-nowrap">{{ $item->date->format('M d,
                                Y') }}</td>
                            <td class="px-4 py-3 text-sm text-white font-medium">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-300">{{ $item->description ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $item->is_company_wide ? 'bg-lime-500/20 text-lime-400' : 'bg-slate-700 text-slate-300' }}">
                                    {{ $item->is_company_wide ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $item->is_active ? 'bg-blue-500/20 text-blue-300' : 'bg-slate-700 text-slate-300' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.hrm.holidays.edit', $item) }}"
                                        class="text-lime-400 hover:text-lime-300">Edit</a>
                                    <button type="button" onclick="openDeleteModal('delete-holiday-{{ $item->id }}')"
                                        class="text-red-400 hover:text-red-300">Delete</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">No holidays added yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $holidays->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Modals -->
@foreach($holidays as $item)
<div id="delete-holiday-{{ $item->id }}"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg max-w-md w-full p-6 border border-slate-700">
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-semibold text-white mb-2">Delete Holiday</h3>
                <p class="text-slate-300 text-sm">Are you sure you want to delete "{{ $item->name }}" on {{ $item->date
                    }}? This action cannot be undone.</p>
            </div>
        </div>
        <div class="flex gap-3 justify-end">
            <button type="button" onclick="closeDeleteModal('delete-holiday-{{ $item->id }}')"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                Cancel
            </button>
            <form action="{{ route('admin.hrm.holidays.destroy', $item) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                    Delete Holiday
                </button>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    function openDeleteModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeDeleteModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Close on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="delete-holiday-"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});

// Close on background click
document.querySelectorAll('[id^="delete-holiday-"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal(this.id);
    });
});
</script>
@endpush
@endsection