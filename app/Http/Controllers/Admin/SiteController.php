<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::withCount([
            'teamMembers',
            'newsMedia',
            'careers',
            'caseStudies',
            'blogs',
            'contactForms',
            'bookingForms',
            'services',
            'scheduleMeetings',
            'hirings',
            'companiesList'
        ])->get();

        return view('admin.sites.index', compact('sites'));
    }

    public function dashboard(Site $site)
    {
        // Load counts for the specific site
        $site->loadCount([
            'teamMembers',
            'newsMedia',
            'careers',
            'caseStudies',
            'blogs',
            'contactForms',
            'bookingForms',
            'services',
            'scheduleMeetings',
            'hirings',
            'companiesList'
        ]);

        return view('admin.sites.dashboard', compact('site'));
    }

    public function create()
    {
        return view('admin.sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:sites,slug',
            'domain' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Site::create($validated);

        return redirect()->route('admin.sites.index')
            ->with('success', 'Site created successfully.');
    }

    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:sites,slug,' . $site->id,
            'domain' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $site->update($validated);

        return redirect()->route('admin.sites.index')
            ->with('success', 'Site updated successfully.');
    }

    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('admin.sites.index')
            ->with('success', 'Site deleted successfully.');
    }
}
