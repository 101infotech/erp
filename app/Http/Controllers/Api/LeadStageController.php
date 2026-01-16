<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeadStage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadStageController extends Controller
{
    /**
     * Get all available lead stages
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $stages = LeadStage::active()->get();

            return response()->json([
                'success' => true,
                'data' => $stages,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stages',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get a specific stage with its leads
     */
    public function show(LeadStage $stage, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $leads = $stage->leads()
                ->with(['leadOwner', 'leadStage'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'stage' => $stage,
                    'leads' => $leads->items(),
                    'pagination' => [
                        'total' => $leads->total(),
                        'per_page' => $leads->perPage(),
                        'current_page' => $leads->currentPage(),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stage details',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get stage pipeline with all stages and lead counts
     */
    public function pipeline(Request $request): JsonResponse
    {
        try {
            $pipeline = [];
            $stages = LeadStage::active()->get();

            foreach ($stages as $stage) {
                $leadCount = $stage->leads()->open()->count();
                $pipeline[] = [
                    'stage_id' => $stage->id,
                    'stage_number' => $stage->stage_number,
                    'stage_name' => $stage->stage_name,
                    'description' => $stage->description,
                    'lead_count' => $leadCount,
                    'auto_timeout_days' => $stage->auto_timeout_days,
                    'requires_action' => $stage->requires_action,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $pipeline,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pipeline',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get stage with stage transitions
     */
    public function transitionInfo(LeadStage $stage): JsonResponse
    {
        try {
            // Get next stage
            $nextStage = LeadStage::active()
                ->where('stage_number', '>', $stage->stage_number)
                ->orderBy('stage_number', 'asc')
                ->first();

            // Get previous stage
            $previousStage = LeadStage::active()
                ->where('stage_number', '<', $stage->stage_number)
                ->orderBy('stage_number', 'desc')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'current_stage' => $stage,
                    'next_stage' => $nextStage,
                    'previous_stage' => $previousStage,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transition info',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get stage metrics
     */
    public function metrics(Request $request): JsonResponse
    {
        try {
            $metrics = [];
            $stages = LeadStage::active()->get();

            foreach ($stages as $stage) {
                $leads = $stage->leads()->open();
                $totalLeads = $leads->count();
                $highPriority = $leads->where('priority', 'high')->count();
                $urgentPriority = $leads->where('priority', 'urgent')->count();

                $metrics[] = [
                    'stage_id' => $stage->id,
                    'stage_name' => $stage->stage_name,
                    'total_leads' => $totalLeads,
                    'high_priority' => $highPriority,
                    'urgent_priority' => $urgentPriority,
                    'average_duration_days' => $stage->auto_timeout_days,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $metrics,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve metrics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
