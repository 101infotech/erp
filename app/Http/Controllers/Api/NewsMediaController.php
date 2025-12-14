<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsMedia;
use Illuminate\Http\Request;

class NewsMediaController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsMedia::where('is_published', true);

        if ($request->filled('site_slug')) {
            $query->whereHas('site', function ($q) use ($request) {
                $q->where('slug', $request->site_slug);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', true);
        }

        $perPage = $request->get('per_page', 10);
        $newsMedia = $query->latest('published_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $newsMedia,
        ]);
    }

    public function show($slug)
    {
        $newsMedia = NewsMedia::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $newsMedia,
        ]);
    }
}
