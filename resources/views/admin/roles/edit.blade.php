@extends('admin.layouts.app')

@section('title', 'Edit Role Permissions')
@section('page-title', 'Edit Role: ' . $role->name)

@section('content')
<div class="px-6 md:px-8 space-y-6">
    <div class="bg-slate-800/50 border border-slate-700 rounded-xl">
        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 border-b border-slate-700">
                <h2 class="text-white font-semibold">Permissions</h2>
                <p class="text-xs text-slate-400">Assign granular permissions to this role.</p>
            </div>
            <div class="p-5 space-y-6">
                @foreach($permissionsByModule as $module => $perms)
                <div class="border border-slate-700 rounded-lg">
                    <div class="px-4 py-2 bg-slate-900/50 border-b border-slate-700">
                        <h3 class="text-slate-200 font-medium text-sm uppercase">{{ $module }}</h3>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($perms as $slug => $label)
                        <label class="flex items-center gap-3 text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $slug }}"
                                class="rounded border-slate-600 bg-slate-900" {{ in_array($slug, $rolePermissions)
                                ? 'checked' : '' }}>
                            <span class="text-slate-300">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-4 border-t border-slate-700 bg-slate-900/40 flex items-center justify-end gap-3">
                <a href="{{ route('admin.roles.index') }}"
                    class="px-4 py-2 rounded bg-slate-700/40 text-slate-300 text-sm">Cancel</a>
                <button type="submit" class="px-4 py-2 rounded bg-lime-500 text-slate-900 font-semibold text-sm">Save
                    Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection