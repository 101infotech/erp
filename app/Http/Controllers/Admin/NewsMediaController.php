<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsMedia;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsMediaController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsMedia::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $newsMedia = $query->latest()->paginate(15);
        $sites = Site::where('is_active', true)->get();

        return view('admin.news-media.index', compact('newsMedia', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->get();
        $selectedSiteId = session('selected_site_id');
        return view('admin.news-media.create', compact('sites', 'selectedSiteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:news_media,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        NewsMedia::create($validated);

        return redirect()->route('admin.news-media.index')
            ->with('success', 'News created successfully.');
    }

    public function edit(NewsMedia $newsMedia)
    {
        $sites = Site::where('is_active', true)->get();
        return view('admin.news-media.edit', compact('newsMedia', 'sites'));
    }

    public function update(Request $request, NewsMedia $newsMedia)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:news_media,slug,' . $newsMedia->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'category' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($newsMedia->featured_image) {
                Storage::disk('public')->delete($newsMedia->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        $newsMedia->update($validated);

        return redirect()->route('admin.news-media.index')
            ->with('success', 'News updated successfully.');
    }

    public function destroy(NewsMedia $newsMedia)
    {
        if ($newsMedia->featured_image) {
            Storage::disk('public')->delete($newsMedia->featured_image);
        }

        $newsMedia->delete();

        return redirect()->route('admin.news-media.index')
            ->with('success', 'News deleted successfully.');
    }
}
