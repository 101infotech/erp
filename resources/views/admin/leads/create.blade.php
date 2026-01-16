@extends('admin.layouts.app')

@section('title', 'Create Service Lead')
@section('page-title', 'Create Service Lead')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-4xl">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-slate-400 mb-2">
                <a href="{{ route('admin.leads.index') }}" class="hover:text-lime-400 transition">Service Leads</a>
                <span>/</span>
                <span class="text-white">Create New Lead</span>
            </div>
            <h2 class="text-2xl font-bold text-white">Create New Service Lead</h2>
            <p class="text-slate-400 mt-1">Add a new service request to the system</p>
        </div>

        <form action="{{ route('admin.leads.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Client Information -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-lime-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Client Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="client_name" class="block text-sm font-medium text-slate-300 mb-2">
                            Client Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="client_name" name="client_name" required value="{{ old('client_name') }}"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('client_name') border-red-500 @enderror">
                        @error('client_name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                            Email <span class="text-red-400">*</span>
                        </label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-slate-300 mb-2">
                            Phone Number <span class="text-red-400">*</span>
                        </label>
                        <input type="tel" id="phone_number" name="phone_number" required
                            value="{{ old('phone_number') }}"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('phone_number') border-red-500 @enderror">
                        @error('phone_number')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-slate-300 mb-2">
                            Location/Address <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="location" name="location" required value="{{ old('location') }}"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('location') border-red-500 @enderror">
                        @error('location')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Service Details -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-lime-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 12h18" />
                        <path d="M3 6h18" />
                        <path d="M3 18h18" />
                    </svg>
                    Service Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="service_requested" class="block text-sm font-medium text-slate-300 mb-2">
                            Service Type <span class="text-red-400">*</span>
                        </label>
                        <select id="service_requested" name="service_requested" required
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('service_requested') border-red-500 @enderror">
                            <option value="">Select a service...</option>
                            <option value="Home Inspection" {{ old('service_requested')=='Home Inspection' ? 'selected'
                                : '' }}>Home Inspection</option>
                            <option value="Commercial Inspection" {{ old('service_requested')=='Commercial Inspection'
                                ? 'selected' : '' }}>Commercial Inspection</option>
                            <option value="New Construction" {{ old('service_requested')=='New Construction'
                                ? 'selected' : '' }}>New Construction</option>
                            <option value="Renovation" {{ old('service_requested')=='Renovation' ? 'selected' : '' }}>
                                Renovation</option>
                            <option value="Basement Development" {{ old('service_requested')=='Basement Development'
                                ? 'selected' : '' }}>Basement Development</option>
                            <option value="Kitchen Remodel" {{ old('service_requested')=='Kitchen Remodel' ? 'selected'
                                : '' }}>Kitchen Remodel</option>
                            <option value="Bathroom Remodel" {{ old('service_requested')=='Bathroom Remodel'
                                ? 'selected' : '' }}>Bathroom Remodel</option>
                            <option value="Deck/Patio" {{ old('service_requested')=='Deck/Patio' ? 'selected' : '' }}>
                                Deck/Patio</option>
                            <option value="Flooring" {{ old('service_requested')=='Flooring' ? 'selected' : '' }}>
                                Flooring
                            </option>
                            <option value="Painting" {{ old('service_requested')=='Painting' ? 'selected' : '' }}>
                                Painting
                            </option>
                            <option value="Roofing" {{ old('service_requested')=='Roofing' ? 'selected' : '' }}>Roofing
                            </option>
                            <option value="Siding" {{ old('service_requested')=='Siding' ? 'selected' : '' }}>Siding
                            </option>
                            <option value="Windows/Doors" {{ old('service_requested')=='Windows/Doors' ? 'selected' : ''
                                }}>
                                Windows/Doors</option>
                            <option value="Plumbing" {{ old('service_requested')=='Plumbing' ? 'selected' : '' }}>
                                Plumbing
                            </option>
                            <option value="Electrical" {{ old('service_requested')=='Electrical' ? 'selected' : '' }}>
                                Electrical</option>
                        </select>
                        @error('service_requested')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-300 mb-2">
                            Status <span class="text-red-400">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('status') border-red-500 @enderror">
                            @foreach($statuses as $status)
                            <option value="{{ $status->status_key }}" {{ old('status', 'Intake' )==$status->status_key ?
                                'selected' : '' }}>
                                {{ $status->display_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('status')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-slate-300 mb-2">
                            Client Message/Notes
                        </label>
                        <textarea id="message" name="message" rows="3"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Inspection Details -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Inspection Schedule
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="inspection_date" class="block text-sm font-medium text-slate-300 mb-2">
                            Inspection Date
                        </label>
                        <input type="text" id="inspection_date" name="inspection_date"
                            value="{{ old('inspection_date') }}" placeholder="Select date..."
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('inspection_date') border-red-500 @enderror">
                        @error('inspection_date')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="inspection_time" class="block text-sm font-medium text-slate-300 mb-2">
                            Inspection Time
                        </label>
                        <input type="text" id="inspection_time" name="inspection_time"
                            value="{{ old('inspection_time') }}" placeholder="Select time..."
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('inspection_time') border-red-500 @enderror">
                        @error('inspection_time')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="inspection_charge" class="block text-sm font-medium text-slate-300 mb-2">
                            Inspection Charge ($)
                        </label>
                        <input type="number" id="inspection_charge" name="inspection_charge"
                            value="{{ old('inspection_charge') }}" step="0.01" min="0" placeholder="0.00"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('inspection_charge') border-red-500 @enderror">
                        @error('inspection_charge')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Project & Assignment -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Project & Assignment
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="project_size" class="block text-sm font-medium text-slate-300 mb-2">
                            Project Size
                        </label>
                        <input type="text" id="project_size" name="project_size" value="{{ old('project_size') }}"
                            placeholder="e.g., 2000 sq ft, 3 bedroom"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('project_size') border-red-500 @enderror">
                        @error('project_size')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-slate-300 mb-2">
                            Assign To
                        </label>
                        <select id="assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('assigned_to') border-red-500 @enderror">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to')==$user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="timeline" class="block text-sm font-medium text-slate-300 mb-2">
                            Project Timeline
                        </label>
                        <input type="text" id="timeline" name="timeline" value="{{ old('timeline') }}"
                            placeholder="e.g., 2-3 months, ASAP, Fall 2024"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('timeline') border-red-500 @enderror">
                        @error('timeline')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="budget" class="block text-sm font-medium text-slate-300 mb-2">
                            Budget Range
                        </label>
                        <input type="text" id="budget" name="budget" value="{{ old('budget') }}"
                            placeholder="e.g., $10,000 - $50,000"
                            class="w-full px-3 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500 @error('budget') border-red-500 @enderror">
                        @error('budget')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.leads.index') }}"
                    class="px-6 py-2.5 bg-slate-700 text-white rounded-lg font-medium hover:bg-slate-600 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Create Lead</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Date picker
    flatpickr("#inspection_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        altInput: true,
        altFormat: "F j, Y"
    });

    // Time picker
    flatpickr("#inspection_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: false,
        altInput: true,
        altFormat: "h:i K"
    });
</script>
@endpush

@endsection