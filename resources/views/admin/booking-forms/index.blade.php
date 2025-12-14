@extends('admin.layouts.app')

@section('title', 'Booking Forms')
@section('page-title', 'Booking Form Submissions')

@section('content')
<div class="bg-slate-800 rounded-lg shadow border border-slate-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-700">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Name</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden md:table-cell">
                    Email</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden lg:table-cell">
                    Phone</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden sm:table-cell">
                    Service</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-slate-800 divide-y divide-slate-700">
            @forelse($bookingForms as $booking)
            <tr class="{{ $booking->status === 'new' ? 'bg-red-500/10' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-white">{{ $booking->first_name }} {{ $booking->last_name }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-300">{{ $booking->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-300">{{ $booking->service_type }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $booking->status === 'new' ? 'bg-red-500/20 text-red-400' : 'bg-slate-500/20 text-slate-400' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                    {{ $booking->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('workspace.booking-forms.show', ['workspace' => request()->segment(1), 'booking_form' => $booking]) }}"
                        class="text-lime-400 hover:text-lime-300">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-slate-400">No booking forms found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($bookingForms->hasPages())
<div class="mt-4">
    {{ $bookingForms->links() }}
</div>
@endif
@endsection