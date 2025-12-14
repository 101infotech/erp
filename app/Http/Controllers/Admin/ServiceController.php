<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $siteId = $request->query('site');

        $query = Service::with('site');

        if ($siteId) {
            $query->where('site_id', $siteId);
        }

        $services = $query->orderBy('order')->latest()->paginate(15);
        $sites = Site::where('is_active', true)->get();
        $selectedSite = $siteId ? Site::find($siteId) : null;

        return view('admin.services.index', compact('services', 'sites', 'selectedSite'));
    }

    public function create(Request $request)
    {
        $sites = Site::where('is_active', true)->get();
        $selectedSiteId = $request->query('site');
        return view('admin.services.create', compact('sites', 'selectedSiteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:services,slug',
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'features' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('services', 'public');
        }

        if (isset($validated['features'])) {
            $validated['features'] = array_map('trim', explode(',', $validated['features']));
        }

        Service::create($validated);

        return redirect()->route('admin.services.index', ['site' => $validated['site_id']])
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $sites = Site::where('is_active', true)->get();
        return view('admin.services.edit', compact('service', 'sites'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:services,slug,' . $service->id,
            'description' => 'nullable|string',
            'details' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'features' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($service->featured_image) {
                Storage::disk('public')->delete($service->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('services', 'public');
        }

        if (isset($validated['features'])) {
            $validated['features'] = array_map('trim', explode(',', $validated['features']));
        }

        $service->update($validated);

        return redirect()->route('admin.services.index', ['site' => $validated['site_id']])
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $siteId = $service->site_id;

        if ($service->featured_image) {
            Storage::disk('public')->delete($service->featured_image);
        }

        $service->delete();

        return redirect()->route('admin.services.index', ['site' => $siteId])
            ->with('success', 'Service deleted successfully.');
    }
}
