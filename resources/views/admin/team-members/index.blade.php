@extends('admin.layouts.app')

@section('title', 'Team Members')
@section('page-title', 'Manage Team Members')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('workspace.team-members.create', ['workspace' => request()->segment(1)]) }}"
        class="bg-lime-500 hover:bg-lime-600 text-slate-950 font-medium px-6 py-2 rounded-lg">
        Add New Member
    </a>
</div>

<div class="bg-slate-800 rounded-lg shadow border border-slate-700 overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-700">
        <thead class="bg-slate-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Member</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Position
                </th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider hidden sm:table-cell">
                    Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-slate-800 divide-y divide-slate-700">
            @forelse($teamMembers as $member)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($member->image_path)
                        <img src="{{ Storage::url($member->image_path) }}" alt="{{ $member->name }}"
                            class="w-10 h-10 rounded-full mr-3">
                        @else
                        <div class="w-10 h-10 rounded-full bg-slate-700 mr-3"></div>
                        @endif
                        <div class="text-sm font-medium text-white">{{ $member->name }}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-300">{{ $member->position }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                    <div class="text-sm text-slate-300">{{ $member->order }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('workspace.team-members.edit', ['workspace' => request()->segment(1), 'team_member' => $member]) }}"
                        class="text-lime-400 hover:text-lime-300 mr-3">Edit</a>
                    <form
                        action="{{ route('workspace.team-members.destroy', ['workspace' => request()->segment(1), 'team_member' => $member]) }}"
                        method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-slate-400">No team members found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($teamMembers->hasPages())
<div class="mt-4">
    {{ $teamMembers->links() }}
</div>
@endif
@endsection