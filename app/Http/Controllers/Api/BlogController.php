<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::where('is_published', true);

        if ($request->filled('site_slug')) {
            $query->whereHas('site', function ($q) use ($request) {
                $q->where('slug', $request->site_slug);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('author')) {
            $query->where('author', $request->author);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $perPage = $request->get('per_page', 10);
        $blogs = $query->latest('published_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $blogs,
        ]);
    }

    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $blog,
        ]);
    }
}
