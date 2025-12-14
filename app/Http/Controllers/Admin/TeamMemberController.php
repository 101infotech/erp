<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamMember::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $teamMembers = $query->orderBy('order')->paginate(15);
        $sites = Site::where('is_active', true)->get();

        return view('admin.team-members.index', compact('teamMembers', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->get();
        $selectedSiteId = session('selected_site_id');
        return view('admin.team-members.create', compact('sites', 'selectedSiteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('team-members', 'public');
        }

        TeamMember::create($validated);

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Team member created successfully.');
    }

    public function edit(TeamMember $teamMember)
    {
        $sites = Site::where('is_active', true)->get();
        return view('admin.team-members.edit', compact('teamMember', 'sites'));
    }

    public function update(Request $request, TeamMember $teamMember)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'order' => 'integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($teamMember->image) {
                Storage::disk('public')->delete($teamMember->image);
            }
            $validated['image'] = $request->file('image')->store('team-members', 'public');
        }

        $teamMember->update($validated);

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Team member updated successfully.');
    }

    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->image) {
            Storage::disk('public')->delete($teamMember->image);
        }

        $teamMember->delete();

        return redirect()->route('admin.team-members.index')
            ->with('success', 'Team member deleted successfully.');
    }
}
