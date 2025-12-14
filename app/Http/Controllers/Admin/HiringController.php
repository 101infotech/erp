<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hiring;
use App\Models\Site;
use Illuminate\Http\Request;

class HiringController extends Controller
{
    public function index(Request $request)
    {
        $query = Hiring::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $hirings = $query->latest()->paginate(15);
        $sites = Site::where('is_active', true)->get();

        return view('admin.hirings.index', compact('hirings', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->get();
        $selectedSiteId = session('selected_site_id');
        return view('admin.hirings.create', compact('sites', 'selectedSiteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'vacancies' => 'nullable|integer|min:1',
            'status' => 'required|in:open,closed,filled',
            'is_featured' => 'boolean',
        ]);

        if (isset($validated['requirements'])) {
            $validated['requirements'] = array_map('trim', explode("\n", $validated['requirements']));
        }

        if (isset($validated['responsibilities'])) {
            $validated['responsibilities'] = array_map('trim', explode("\n", $validated['responsibilities']));
        }

        Hiring::create($validated);

        return redirect()->route('admin.hirings.index')
            ->with('success', 'Hiring position created successfully.');
    }

    public function edit(Hiring $hiring)
    {
        $sites = Site::where('is_active', true)->get();
        return view('admin.hirings.edit', compact('hiring', 'sites'));
    }

    public function update(Request $request, Hiring $hiring)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'vacancies' => 'nullable|integer|min:1',
            'status' => 'required|in:open,closed,filled',
            'is_featured' => 'boolean',
        ]);

        if (isset($validated['requirements'])) {
            $validated['requirements'] = array_map('trim', explode("\n", $validated['requirements']));
        }

        if (isset($validated['responsibilities'])) {
            $validated['responsibilities'] = array_map('trim', explode("\n", $validated['responsibilities']));
        }

        $hiring->update($validated);

        return redirect()->route('admin.hirings.index')
            ->with('success', 'Hiring position updated successfully.');
    }

    public function destroy(Hiring $hiring)
    {
        $hiring->delete();

        return redirect()->route('admin.hirings.index')
            ->with('success', 'Hiring position deleted successfully.');
    }
}
