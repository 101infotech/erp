@extends('admin.layouts.app')

@section('title', 'Assign Users to Role')
@section('page-title', 'Assign Users: ' . $role->name)

@section('content')
<div class="px-6 md:px-8 space-y-6">
    <div class="bg-slate-800/50 border border-slate-700 rounded-xl">
        <form method="POST" action="{{ route('admin.roles.users.sync', $role) }}">
            @csrf
            <div class="px-5 py-4 border-b border-slate-700">
                <h2 class="text-white font-semibold">Users</h2>
                <p class="text-xs text-slate-400">Select users to assign to this role.</p>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($users as $user)
                    <label
                        class="flex items-center gap-3 text-sm border border-slate-700 rounded-lg p-3 bg-slate-900/40">
                        <input type="checkbox" name="users[]" value="{{ $user->id }}"
                            class="rounded border-slate-600 bg-slate-900" {{ in_array($user->id, $assigned) ? 'checked'
                        : '' }}>
                        <div>
                            <p class="text-white font-medium">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="px-5 py-4 border-t border-slate-700 bg-slate-900/40 flex items-center justify-end gap-3">
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2 rounded bg-slate-700/40 text-slate-300 text-sm">Cancel</a>
                <button type="submit" class="px-4 py-2 rounded bg-lime-500 text-slate-900 font-semibold text-sm">Save
                    Assignments</button>
            </div>
        </form>
    </div>
</div>
@endsection