<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScheduleMeeting;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleMeetingController extends Controller
{
    /**
     * Store a new meeting request for Saubhagya Group
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'site_slug' => 'nullable|exists:sites,slug',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|string|max:50',
            'meeting_type' => 'required|string|in:Partnership Discussion,Investment Opportunity,Franchise Inquiry,Project Consultation,General Inquiry',
            'message' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Get site if provided, otherwise use default or first site
        $site = null;
        if (isset($validated['site_slug'])) {
            $site = Site::where('slug', $validated['site_slug'])->first();
        } else {
            // Get Saubhagya Group site or first available site
            $site = Site::where('slug', 'saubhagya-group')->first() ?? Site::first();
        }

        if (!$site) {
            return response()->json([
                'success' => false,
                'message' => 'Site not found. Please contact support.',
            ], 404);
        }

        // Create the meeting request
        $meeting = ScheduleMeeting::create([
            'site_id' => $site->id,
            'full_name' => $validated['full_name'],
            'name' => $validated['full_name'], // Keeping legacy field for compatibility
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'meeting_type' => $validated['meeting_type'],
            'subject' => $validated['meeting_type'], // Use meeting type as subject
            'message' => $validated['message'] ?? null,
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting request submitted successfully. We will get back to you within 24 hours.',
            'data' => [
                'id' => $meeting->id,
                'full_name' => $meeting->full_name,
                'email' => $meeting->email,
                'meeting_type' => $meeting->meeting_type,
                'preferred_date' => $meeting->preferred_date->format('Y-m-d'),
                'preferred_time' => $meeting->preferred_time,
                'status' => $meeting->status,
                'created_at' => $meeting->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * Get all meeting requests (Admin only)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = ScheduleMeeting::with('site')->latest();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by site if provided
        if ($request->has('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $meetings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $meetings,
        ], 200);
    }

    /**
     * Get a specific meeting request
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $meeting = ScheduleMeeting::with('site')->find($id);

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting request not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $meeting,
        ], 200);
    }

    /**
     * Update meeting status
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $meeting = ScheduleMeeting::find($id);

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting request not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $meeting->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting status updated successfully',
            'data' => $meeting,
        ], 200);
    }

    /**
     * Delete a meeting request
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $meeting = ScheduleMeeting::find($id);

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting request not found',
            ], 404);
        }

        $meeting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meeting request deleted successfully',
        ], 200);
    }
}
