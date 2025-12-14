<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $query = Career::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $careers = $query->latest()->paginate(15);
        $sites = Site::where('is_active', true)->get();

        return view('admin.careers.index', compact('careers', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->get();
        $selectedSiteId = session('selected_site_id');
        return view('admin.careers.create', compact('sites', 'selectedSiteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:careers,slug',
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:full-time,part-time,contract,internship',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'posted_at' => 'nullable|date',
            'deadline' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        Career::create($validated);

        return redirect()->route('admin.careers.index')
            ->with('success', 'Career created successfully.');
    }

    public function edit(Career $career)
    {
        $sites = Site::where('is_active', true)->get();
        return view('admin.careers.edit', compact('career', 'sites'));
    }

    public function update(Request $request, Career $career)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:careers,slug,' . $career->id,
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:full-time,part-time,contract,internship',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'posted_at' => 'nullable|date',
            'deadline' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $career->update($validated);

        return redirect()->route('admin.careers.index')
            ->with('success', 'Career updated successfully.');
    }

    public function destroy(Career $career)
    {
        $career->delete();

        return redirect()->route('admin.careers.index')
            ->with('success', 'Career deleted successfully.');
    }
}
