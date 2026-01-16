<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreFollowUpRequest;
use App\Http\Requests\Api\UpdateFollowUpRequest;
use App\Models\ServiceLead;
use App\Models\LeadFollowUp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadFollowUpController extends Controller
{
    /**
     * Get all follow-ups for a lead
     */
    public function index(ServiceLead $lead, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $followUps = $lead->followUps()
                ->with('followUpOwner')
                ->orderBy('follow_up_date', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $followUps->items(),
                'pagination' => [
                    'total' => $followUps->total(),
                    'per_page' => $followUps->perPage(),
                    'current_page' => $followUps->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve follow-ups',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Create a new follow-up for a lead
     */
    public function store(ServiceLead $lead, StoreFollowUpRequest $request): JsonResponse
    {
        try {
            $followUp = $lead->followUps()->create(array_merge(
                $request->validated(),
                ['follow_up_owner_id' => auth()->id()]
            ));

            // Update lead follow-up tracking
            $lead->increment('follow_up_count');
            $lead->update(['last_activity_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Follow-up created successfully',
                'data' => $followUp->load('followUpOwner'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create follow-up',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display a specific follow-up
     */
    public function show(ServiceLead $lead, LeadFollowUp $followUp): JsonResponse
    {
        try {
            if ($followUp->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Follow-up does not belong to this lead',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $followUp->load('followUpOwner'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve follow-up',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update a follow-up
     */
    public function update(ServiceLead $lead, LeadFollowUp $followUp, UpdateFollowUpRequest $request): JsonResponse
    {
        try {
            if ($followUp->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Follow-up does not belong to this lead',
                ], 404);
            }

            $followUp->update($request->validated());

            // Update lead last activity
            $lead->update(['last_activity_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Follow-up updated successfully',
                'data' => $followUp->load('followUpOwner'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update follow-up',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete a follow-up
     */
    public function destroy(ServiceLead $lead, LeadFollowUp $followUp): JsonResponse
    {
        try {
            if ($followUp->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Follow-up does not belong to this lead',
                ], 404);
            }

            $followUp->delete();

            return response()->json([
                'success' => true,
                'message' => 'Follow-up deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete follow-up',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get pending follow-ups
     */
    public function pending(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $followUps = LeadFollowUp::pending()
                ->with(['lead', 'followUpOwner'])
                ->orderBy('next_follow_up_date', 'asc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $followUps->items(),
                'pagination' => [
                    'total' => $followUps->total(),
                    'per_page' => $followUps->perPage(),
                    'current_page' => $followUps->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pending follow-ups',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get follow-ups by type
     */
    public function byType(Request $request): JsonResponse
    {
        try {
            $request->validate(['type' => 'required|in:call,visit,whatsapp,email,sms']);

            $perPage = $request->get('per_page', 15);
            $followUps = LeadFollowUp::byType($request->type)
                ->with(['lead', 'followUpOwner'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $followUps->items(),
                'pagination' => [
                    'total' => $followUps->total(),
                    'per_page' => $followUps->perPage(),
                    'current_page' => $followUps->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve follow-ups',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
