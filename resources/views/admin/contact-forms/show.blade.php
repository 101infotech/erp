@extends('admin.layouts.app')

@section('title', 'Contact Form Details')
@section('page-title', 'Contact Form Submission')

@section('content')
<div class="max-w-3xl">
    <div class="bg-slate-800 rounded-lg shadow border border-slate-700 p-6">
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $contactForm->name }}</h3>
                <p class="text-sm text-gray-500">{{ $contactForm->site->name }} • {{ $contactForm->created_at->format('M
                    d, Y g:i A') }}</p>
            </div>
            <span
                class="px-3 py-1 text-sm rounded-full {{ $contactForm->status === 'new' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($contactForm->status) }}
            </span>
        </div>

        <div class="border-t pt-6">
            <dl class="grid grid-cols-1 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $contactForm->email }}</dd>
                </div>

                @if($contactForm->phone)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $contactForm->phone }}</dd>
                </div>
                @endif

                @if($contactForm->subject)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Subject</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $contactForm->subject }}</dd>
                </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-500">Message</dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $contactForm->message }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $contactForm->ip_address ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <div class="mt-8 flex space-x-3">
            @if($contactForm->status === 'new')
            <form action="{{ route('admin.contact-forms.update', $contactForm) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="read">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    Mark as Read
                </button>
            </form>
            @endif
            <a href="{{ route('admin.contact-forms.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection