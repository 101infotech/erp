@extends('admin.layouts.app')

@section('title', 'Booking Form Details')
@section('page-title', 'Booking Form Submission')

@section('content')
<div class="max-w-3xl">
    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $bookingForm->first_name }} {{
                    $bookingForm->last_name }}</h3>
                <p class="text-sm text-gray-500">{{ $bookingForm->site->name }} • {{ $bookingForm->created_at->format('M
                    d, Y g:i A') }}</p>
            </div>
            <span
                class="px-3 py-1 text-sm rounded-full {{ $bookingForm->status === 'new' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($bookingForm->status) }}
            </span>
        </div>

        <div class="border-t pt-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">First Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->first_name }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->last_name }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->email }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->phone }}</dd>
                </div>

                @if($bookingForm->company_name)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->company_name }}</dd>
                </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-500">Service Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->service_type }}</dd>
                </div>

                @if($bookingForm->preferred_date)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Preferred Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{
                        \Carbon\Carbon::parse($bookingForm->preferred_date)->format('M d, Y') }}</dd>
                </div>
                @endif

                @if($bookingForm->budget)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Budget</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->budget }}</dd>
                </div>
                @endif

                <div class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Project Details</dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $bookingForm->project_details ?? 'N/A'
                        }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $bookingForm->ip_address ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="mt-8 flex space-x-3">
            @if($bookingForm->status === 'new')
            <form action="{{ route('admin.booking-forms.update', $bookingForm) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="contacted">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    Mark as Contacted
                </button>
            </form>
            @endif
            <a href="{{ route('admin.booking-forms.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection