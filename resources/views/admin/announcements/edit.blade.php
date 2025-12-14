@extends('admin.layouts.app')

@section('title', 'Edit Announcement')
@section('page-title', 'Edit Announcement')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('admin.announcements.index') }}"
                class="px-3 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                ← Back
            </a>
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Announcement</h2>
                <p class="text-slate-400 mt-1">Update announcement details</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-6">
        <ul class="list-disc list-inside text-red-400">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST"
        class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Title <span class="text-red-400">*</span>
            </label>
            <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required
                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                placeholder="Enter announcement title">
        </div>

        <!-- Content -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Content <span class="text-red-400">*</span>
            </label>
            <textarea name="content" rows="8" required
                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white placeholder-slate-400 rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500"
                placeholder="Enter announcement content">{{ old('content', $announcement->content) }}</textarea>
        </div>

        <!-- Priority -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Priority <span class="text-red-400">*</span>
            </label>
            <select name="priority" required
                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="low" {{ old('priority', $announcement->priority) == 'low' ? 'selected' : '' }}>Low -
                    General information</option>
                <option value="normal" {{ old('priority', $announcement->priority) == 'normal' ? 'selected' : ''
                    }}>Normal - Standard updates</option>
                <option value="high" {{ old('priority', $announcement->priority) == 'high' ? 'selected' : '' }}>High -
                    Urgent/Important</option>
            </select>
        </div>

        <!-- Recipients -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Send To <span class="text-red-400">*</span>
            </label>
            <select name="recipient_type" id="recipient_type" required
                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                <option value="all" {{ old('recipient_type', $announcement->recipient_type) == 'all' ? 'selected' : ''
                    }}>All Staff Members</option>
                <option value="specific" {{ old('recipient_type', $announcement->recipient_type) == 'specific' ?
                    'selected' : '' }}>Specific Employees</option>
            </select>
        </div>

        <!-- Specific Recipients -->
        <div id="specific_recipients"
            class="mb-6 {{ old('recipient_type', $announcement->recipient_type) == 'specific' ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-slate-300 mb-2">
                Select Recipients
            </label>
            <select name="recipient_ids[]" multiple size="8"
                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-lime-500 focus:border-lime-500">
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ in_array($user->id, old('recipient_ids', $announcement->recipient_ids
                    ?? [])) ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                </option>
                @endforeach
            </select>
            <p class="text-slate-500 text-sm mt-1">Hold Ctrl/Cmd to select multiple recipients</p>
        </div>

        <!-- Options -->
        <div class="mb-6 space-y-3">
            <label class="flex items-center">
                <input type="checkbox" name="send_email" value="1" {{ old('send_email', $announcement->send_email) ?
                'checked' : '' }}
                class="w-4 h-4 bg-slate-700 border-slate-600 text-lime-500 rounded focus:ring-2 focus:ring-lime-500">
                <span class="ml-2 text-sm text-slate-300">Send email notification to recipients</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $announcement->is_published)
                ? 'checked' : '' }}
                class="w-4 h-4 bg-slate-700 border-slate-600 text-lime-500 rounded focus:ring-2 focus:ring-lime-500">
                <span class="ml-2 text-sm text-slate-300">Published</span>
            </label>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-700">
            <button type="submit"
                class="px-6 py-2 bg-lime-500 text-slate-950 rounded-lg hover:bg-lime-400 font-semibold transition">
                Update Announcement
            </button>
            <a href="{{ route('admin.announcements.index') }}"
                class="px-6 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.getElementById('recipient_type').addEventListener('change', function() {
        const specificDiv = document.getElementById('specific_recipients');
        if (this.value === 'specific') {
            specificDiv.classList.remove('hidden');
        } else {
            specificDiv.classList.add('hidden');
        }
    });
</script>

@endsection