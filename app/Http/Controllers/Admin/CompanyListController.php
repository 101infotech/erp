<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyList;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyListController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyList::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $companies = $query->orderBy('order')->latest()->paginate(15);
        $sites = Site::where('is_active', true)->get();

        return view('admin.companies-list.index', compact('companies', 'sites'));
    }

    public function create()
    {
        $sites = Site::where('is_active', true)->get();
        $selectedSiteId = session('selected_site_id');
        return view('admin.companies-list.create', compact('sites', 'selectedSiteId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:companies_list,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'founded_year' => 'nullable|string|max:4',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'social_links' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('companies', 'public');
        }

        if (isset($validated['social_links'])) {
            $validated['social_links'] = json_decode($validated['social_links'], true);
        }

        CompanyList::create($validated);

        return redirect()->route('admin.companies-list.index')
            ->with('success', 'Company created successfully.');
    }

    public function edit(CompanyList $companyList)
    {
        $sites = Site::where('is_active', true)->get();
        return view('admin.companies-list.edit', compact('companyList', 'sites'));
    }

    public function update(Request $request, CompanyList $companyList)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:companies_list,slug,' . $companyList->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'founded_year' => 'nullable|string|max:4',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'social_links' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($companyList->logo) {
                Storage::disk('public')->delete($companyList->logo);
            }
            $validated['logo'] = $request->file('logo')->store('companies', 'public');
        }

        if (isset($validated['social_links'])) {
            $validated['social_links'] = json_decode($validated['social_links'], true);
        }

        $companyList->update($validated);

        return redirect()->route('admin.companies-list.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(CompanyList $companyList)
    {
        if ($companyList->logo) {
            Storage::disk('public')->delete($companyList->logo);
        }

        $companyList->delete();

        return redirect()->route('admin.companies-list.index')
            ->with('success', 'Company deleted successfully.');
    }
}
