<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\Site;
use Illuminate\Http\Request;

class TeamController extends Controller
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

        $team = TeamMember::where('site_id', $site->id)
            ->where('status', 'active')
            ->select('id', 'name', 'position', 'bio', 'photo', 'email', 'linkedin_url', 'order', 'created_at')
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Team members retrieved successfully',
            'data' => $team
        ]);
    }
}
