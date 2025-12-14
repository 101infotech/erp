<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CaseStudyController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseStudy::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $caseStudies = $query->latest()->paginate(15);
        $sites = Site::where('is_active', true)->get();

        return view('admin.case-studies.index', compact('caseStudies', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->get();
        $selectedSiteId = session('selected_site_id');
        return view('admin.case-studies.create', compact('sites', 'selectedSiteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:case_studies,slug',
            'client_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'content' => 'required|string',
            'results' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('case-studies', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('case-studies/gallery', 'public');
            }
            $validated['gallery'] = $gallery;
        }

        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        CaseStudy::create($validated);

        return redirect()->route('admin.case-studies.index')
            ->with('success', 'Case study created successfully.');
    }

    public function edit(CaseStudy $caseStudy)
    {
        $sites = Site::where('is_active', true)->get();
        return view('admin.case-studies.edit', compact('caseStudy', 'sites'));
    }

    public function update(Request $request, CaseStudy $caseStudy)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:case_studies,slug,' . $caseStudy->id,
            'client_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'content' => 'required|string',
            'results' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($caseStudy->featured_image) {
                Storage::disk('public')->delete($caseStudy->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('case-studies', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('case-studies/gallery', 'public');
            }
            $validated['gallery'] = $gallery;
        }

        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        $caseStudy->update($validated);

        return redirect()->route('admin.case-studies.index')
            ->with('success', 'Case study updated successfully.');
    }

    public function destroy(CaseStudy $caseStudy)
    {
        if ($caseStudy->featured_image) {
            Storage::disk('public')->delete($caseStudy->featured_image);
        }

        if ($caseStudy->gallery) {
            foreach ($caseStudy->gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $caseStudy->delete();

        return redirect()->route('admin.case-studies.index')
            ->with('success', 'Case study deleted successfully.');
    }
}
