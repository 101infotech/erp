<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\Site;
use Illuminate\Http\Request;

class CaseStudyController extends Controller
{
    public function index(Request $request)
    {
        $brand = $request->query('brand');

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand parameter is required'
            ], 400);
        }

        $site = Site::where('slug', $brand)->first();

        if (!$site) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ], 404);
        }

        $caseStudies = CaseStudy::where('site_id', $site->id)
            ->where('status', 'published')
            ->select('id', 'title', 'slug', 'description', 'featured_image', 'category', 'client_name', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Case studies retrieved successfully',
            'data' => $caseStudies
        ]);
    }

    public function show($slug)
    {
        $caseStudy = CaseStudy::where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$caseStudy) {
            return response()->json([
                'status' => 'error',
                'message' => 'Case study not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Case study retrieved successfully',
            'data' => $caseStudy
        ]);
    }
}
