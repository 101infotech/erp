@extends('admin.layouts.app')

@section('title', 'Services')
@section('page-title', 'Services Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h3 class="text-lg font-semibold text-white mb-1">All Services</h3>
        <p class="text-sm text-slate-400">Manage services for {{ session('selected_site_id') ?
            $sites->find(session('selected_site_id'))->name : 'all sites' }}</p>
    </div>
    <a href="{{ route('admin.services.create') }}"
        class="bg-lime-500 hover:bg-lime-600 text-slate-950 font-medium px-4 py-2 rounded-lg transition">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add New Service
    </a>
</div>

<!-- Filters -->
@if(!session('selected_site_id'))
<div class="mb-6 bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-700">
    <form method="GET" action="{{ route('admin.services.index') }}"
        class="flex gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-slate-300 mb-2">Filter by Site</label>
            <select name="site_id"
                class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-lime-500"
                onchange="this.form.submit()">
                <option value="">All Sites</option>
                @foreach($sites as $site)
                <option value="{{ $site->id }}" {{ request('site_id')==$site->id ? 'selected' : '' }}>
                    {{ $site->name }}
                </option>
                @endforeach
            </select>
        </div>
    </form>
</div>
@endif

<!-- Services Table -->
<div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-900/50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Service
                </th>
                @if(!session('selected_site_id'))
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Site</th>
                @endif
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Order</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Status
                </th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-700">
            @forelse($services as $service)
            <tr class="hover:bg-slate-900/30 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        @if($service->featured_image)
                        <img src="{{ asset('storage/' . $service->featured_image) }}" alt="{{ $service->title }}"
                            class="w-12 h-12 rounded-lg object-cover">
                        @else
                        <div class="w-12 h-12 rounded-lg bg-slate-700 flex items-center justify-center">
                            <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        @endif
                        <div>
                            <div class="text-white font-medium">{{ $service->title }}</div>
                            <div class="text-sm text-slate-400">{{ Str::limit($service->description, 60) }}</div>
                        </div>
                    </div>
                </td>
                @if(!session('selected_site_id'))
                <td class="px-6 py-4">
                    <span
                        class="px-3 py-1 text-xs font-medium rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                        {{ $service->site->name }}
                    </span>
                </td>
                @endif
                <td class="px-6 py-4">
                    <span class="text-slate-300">{{ $service->order }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($service->is_active)
                    <span
                        class="px-3 py-1 text-xs font-medium rounded-full bg-lime-500/10 text-lime-400 border border-lime-500/20">
                        Active
                    </span>
                    @else
                    <span
                        class="px-3 py-1 text-xs font-medium rounded-full bg-slate-500/10 text-slate-400 border border-slate-500/20">
                        Inactive
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.services.edit', $service->id) }}"
                            class="p-2 text-blue-400 hover:bg-blue-500/10 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </a>
                        <button type="button" onclick="openModal('deleteServiceModal_{{ $service->id }}')"
                            class="p-2 text-red-400 hover:bg-red-500/10 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-slate-600 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                    <p class="text-slate-400 text-lg font-medium">No services found</p>
                    <p class="text-slate-500 text-sm mt-2">Get started by creating your first service.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($services->hasPages())
<div class="mt-6">
    {{ $services->links() }}
</div>
@endif

@foreach($services as $service)
<!-- Delete Service Modal -->
<x-professional-modal id="deleteServiceModal_{{ $service->id }}" title="Delete Service"
    subtitle="This action cannot be undone" icon="trash" iconColor="red" maxWidth="max-w-md">
    <div class="space-y-4">
        <p class="text-slate-300">Are you sure you want to delete this service?</p>
        <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700">
            <p class="text-sm text-white"><span class="font-medium">Service:</span> {{ $service->name }}</p>
            <p class="text-sm text-slate-400 mt-1"><span class="font-medium">Type:</span> {{ $service->type }}</p>
        </div>
    </div>
    <x-slot name="footer">
        <button onclick="closeModal('deleteServiceModal_{{ $service->id }}')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">Keep</button>
        <form method="POST"
            action="{{ route('admin.services.destroy', $service->id) }}"
            class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
        </form>
    </x-slot>
</x-professional-modal>
@endforeach
@endsection