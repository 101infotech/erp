@extends('admin.layouts.app')

@section('title', 'Contact Forms')
@section('page-title', 'Contact Form Submissions')

@section('content')
<!-- Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white">Contact Form Submissions</h2>
    <p class="text-slate-400 mt-1">View and manage contact form inquiries</p>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Date
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-slate-300 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-slate-800/50 divide-y divide-slate-700">
                @forelse($contactForms as $contact)
                <tr class="hover:bg-slate-700/50 transition {{ $contact->status === 'new' ? 'bg-red-500/10' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-white">{{ $contact->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-slate-300">{{ $contact->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $contact->status === 'new' ? 'bg-red-500/20 text-red-400' : 'bg-slate-700 text-slate-400' }}">
                            {{ ucfirst($contact->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                        {{ $contact->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('workspace.contact-forms.show', ['workspace' => request()->segment(1), 'contact_form' => $contact]) }}"
                            class="text-lime-400 hover:text-lime-300">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p>No contact forms found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Card View -->
<div class="md:hidden space-y-4">
    @forelse($contactForms as $contact)
    <div
        class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-4 {{ $contact->status === 'new' ? 'border-red-500/30' : '' }}">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="text-sm font-semibold text-white">{{ $contact->name }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $contact->email }}</p>
            </div>
            <span
                class="px-2 py-1 text-xs font-semibold rounded-full {{ $contact->status === 'new' ? 'bg-red-500/20 text-red-400' : 'bg-slate-700 text-slate-400' }}">
                {{ ucfirst($contact->status) }}
            </span>
        </div>

        <div class="text-xs text-slate-400 mb-3">
            {{ $contact->created_at->format('M d, Y') }}
        </div>

        <a href="{{ route('workspace.contact-forms.show', ['workspace' => request()->segment(1), 'contact_form' => $contact]) }}"
            class="block w-full px-3 py-2 text-xs text-center bg-lime-500/20 text-lime-400 rounded-lg hover:bg-lime-500/30 transition">View
            Details</a>
    </div>
    @empty
    <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-8 text-center">
        <svg class="w-12 h-12 mx-auto mb-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <p class="text-slate-400">No contact forms found</p>
    </div>
    @endforelse
</div>

@if($contactForms->hasPages())
<div class="mt-4">
    {{ $contactForms->links() }}
</div>
@endif
@endsection