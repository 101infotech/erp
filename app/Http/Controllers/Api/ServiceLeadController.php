<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreServiceLeadRequest;
use App\Http\Requests\Api\UpdateServiceLeadRequest;
use App\Models\ServiceLead;
use App\Models\LeadStage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceLeadController extends Controller
{
    /**
     * Display a listing of service leads
     */
    public function index(Request $request): JsonResponse
    {
        $query = ServiceLead::with(['leadStage', 'leadOwner', 'followUps', 'payments']);

        // Apply filters
        if ($request->has('stage_id')) {
            $query->where('lead_stage_id', $request->stage_id);
        }

        if ($request->has('owner_id')) {
            $query->where('lead_owner_id', $request->owner_id);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('status') && $request->status === 'open') {
            $query->open();
        }

        if ($request->has('status') && $request->status === 'closed') {
            $query->closed();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $leads = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'pagination' => [
                'total' => $leads->total(),
                'per_page' => $leads->perPage(),
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
            ],
        ], 200);
    }

    /**
     * Store a newly created service lead
     */
    public function store(StoreServiceLeadRequest $request): JsonResponse
    {
        try {
            $lead = ServiceLead::create(array_merge(
                $request->validated(),
                [
                    'lead_stage_id' => 1, // Lead Capture stage
                    'created_by' => auth()->id(),
                    'lead_owner_id' => $request->get('lead_owner_id') ?? auth()->id(),
                ]
            ));

            return response()->json([
                'success' => true,
                'message' => 'Service lead created successfully',
                'data' => $lead->load(['leadStage', 'leadOwner']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service lead',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified service lead
     */
    public function show(ServiceLead $lead): JsonResponse
    {
        try {
            $lead->load([
                'leadStage',
                'leadOwner',
                'siteVisitAssignedTo',
                'followUps' => function ($query) {
                    $query->orderBy('follow_up_date', 'desc');
                },
                'payments' => function ($query) {
                    $query->orderBy('payment_date', 'desc');
                },
                'documents' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'statusInfo',
                'createdBy',
            ]);

            return response()->json([
                'success' => true,
                'data' => $lead,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service lead',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified service lead
     */
    public function update(UpdateServiceLeadRequest $request, ServiceLead $lead): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Update last activity timestamp
            $validated['last_activity_at'] = now();

            $lead->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service lead updated successfully',
                'data' => $lead->load(['leadStage', 'leadOwner']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service lead',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete the specified service lead
     */
    public function destroy(ServiceLead $lead): JsonResponse
    {
        try {
            $lead->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service lead deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service lead',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Transition lead to next stage
     */
    public function transitionStage(Request $request, ServiceLead $lead): JsonResponse
    {
        try {
            $request->validate([
                'stage_id' => 'required|exists:lead_stages,id',
            ]);

            $oldStage = $lead->leadStage->stage_name ?? 'Unknown';
            $newStage = LeadStage::find($request->stage_id);

            $lead->update([
                'lead_stage_id' => $request->stage_id,
                'last_activity_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Lead transitioned from {$oldStage} to {$newStage->stage_name}",
                'data' => $lead->load('leadStage'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to transition lead stage',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get leads needing follow-up
     */
    public function needingFollowUp(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $leads = ServiceLead::needingFollowUp()
                ->with(['leadOwner', 'leadStage'])
                ->orderBy('next_follow_up_date', 'asc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $leads->items(),
                'pagination' => [
                    'total' => $leads->total(),
                    'per_page' => $leads->perPage(),
                    'current_page' => $leads->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leads needing follow-up',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get leads with pending payments
     */
    public function pendingPayment(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $leads = ServiceLead::pendingPayment()
                ->with(['leadOwner', 'leadStage', 'payments'])
                ->orderBy('payment_received_at', 'asc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $leads->items(),
                'pagination' => [
                    'total' => $leads->total(),
                    'per_page' => $leads->perPage(),
                    'current_page' => $leads->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leads with pending payments',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get lead statistics for pipeline
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $stats = [
                'total_leads' => ServiceLead::count(),
                'open_leads' => ServiceLead::open()->count(),
                'closed_leads' => ServiceLead::closed()->count(),
                'by_stage' => [],
                'by_priority' => [
                    'high' => ServiceLead::where('priority', 'high')->count(),
                    'medium' => ServiceLead::where('priority', 'medium')->count(),
                    'low' => ServiceLead::where('priority', 'low')->count(),
                    'urgent' => ServiceLead::where('priority', 'urgent')->count(),
                ],
                'by_payment_status' => [
                    'pending' => ServiceLead::where('payment_status', 'pending')->count(),
                    'partial' => ServiceLead::where('payment_status', 'partial')->count(),
                    'full' => ServiceLead::where('payment_status', 'full')->count(),
                ],
                'needing_follow_up' => ServiceLead::needingFollowUp()->count(),
                'pending_payment' => ServiceLead::pendingPayment()->count(),
            ];

            // Get leads by stage
            $stages = LeadStage::active()->get();
            foreach ($stages as $stage) {
                $stats['by_stage'][] = [
                    'stage_id' => $stage->id,
                    'stage_name' => $stage->stage_name,
                    'count' => $stage->leads()->count(),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Bulk update leads
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'exists:service_leads,id',
                'updates' => 'required|array',
            ]);

            $updated = ServiceLead::whereIn('id', $request->lead_ids)
                ->update(array_merge($request->updates, ['last_activity_at' => now()]));

            return response()->json([
                'success' => true,
                'message' => "{$updated} leads updated successfully",
                'data' => ['updated_count' => $updated],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update leads',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Bulk delete leads
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'lead_ids' => 'required|array',
                'lead_ids.*' => 'exists:service_leads,id',
            ]);

            $deleted = ServiceLead::whereIn('id', $request->lead_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} leads deleted successfully",
                'data' => ['deleted_count' => $deleted],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk delete leads',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
