<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamMember::where('is_active', true);

        if ($request->filled('site_slug')) {
            $query->whereHas('site', function ($q) use ($request) {
                $q->where('slug', $request->site_slug);
            });
        }

        $teamMembers = $query->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $teamMembers,
        ]);
    }

    public function show($id)
    {
        $teamMember = TeamMember::where('is_active', true)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $teamMember,
        ]);
    }
}
