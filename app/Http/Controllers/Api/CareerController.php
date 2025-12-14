<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $query = Career::where('is_active', true);

        if ($request->filled('site_slug')) {
            $query->whereHas('site', function ($q) use ($request) {
                $q->where('slug', $request->site_slug);
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        $careers = $query->latest('posted_at')->get();

        return response()->json([
            'success' => true,
            'data' => $careers,
        ]);
    }

    public function show($slug)
    {
        $career = Career::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $career,
        ]);
    }
}
