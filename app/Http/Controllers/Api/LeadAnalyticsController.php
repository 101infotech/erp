<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceLead;
use App\Models\LeadStage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LeadAnalyticsController extends Controller
{
    /**
     * Get comprehensive dashboard statistics
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $stats = [
                'overview' => [
                    'total_leads' => ServiceLead::count(),
                    'open_leads' => ServiceLead::open()->count(),
                    'closed_leads' => ServiceLead::closed()->count(),
                    'conversion_rate' => $this->calculateConversionRate(),
                ],
                'pipeline' => $this->getPipelineStats(),
                'sales_team' => $this->getSalesTeamStats(),
                'payment' => $this->getPaymentStats(),
                'follow_ups' => $this->getFollowUpStats(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get pipeline stage analytics
     */
    public function pipeline(Request $request): JsonResponse
    {
        try {
            $pipeline = [];
            $stages = LeadStage::active()->get();

            foreach ($stages as $stage) {
                $stageLeads = $stage->leads();
                $totalLeads = $stageLeads->count();
                $openLeads = (clone $stageLeads)->open()->count();

                $pipeline[] = [
                    'stage_id' => $stage->id,
                    'stage_number' => $stage->stage_number,
                    'stage_name' => $stage->stage_name,
                    'total_leads' => $totalLeads,
                    'open_leads' => $openLeads,
                    'closed_leads' => $totalLeads - $openLeads,
                    'percentage_of_total' => ServiceLead::count() > 0 ? round(($totalLeads / ServiceLead::count()) * 100, 2) : 0,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $pipeline,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pipeline analytics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get sales team performance
     */
    public function salesTeam(Request $request): JsonResponse
    {
        try {
            $teamStats = ServiceLead::where('lead_owner_id', '!=', null)
                ->select('lead_owner_id')
                ->selectRaw('COUNT(*) as total_leads')
                ->selectRaw('SUM(CASE WHEN closed_at IS NOT NULL THEN 1 ELSE 0 END) as closed_leads')
                ->selectRaw('SUM(CASE WHEN closed_at IS NULL THEN 1 ELSE 0 END) as open_leads')
                ->selectRaw('SUM(paid_amount) as total_paid')
                ->groupBy('lead_owner_id')
                ->with('leadOwner')
                ->get()
                ->map(function ($stat) {
                    return [
                        'user_id' => $stat->lead_owner_id,
                        'user_name' => $stat->leadOwner->name ?? 'Unknown',
                        'total_leads' => $stat->total_leads,
                        'closed_leads' => $stat->closed_leads,
                        'open_leads' => $stat->open_leads,
                        'conversion_rate' => $stat->total_leads > 0 ? round(($stat->closed_leads / $stat->total_leads) * 100, 2) : 0,
                        'total_revenue' => $stat->total_paid,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $teamStats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales team data',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get payment analytics
     */
    public function payments(Request $request): JsonResponse
    {
        try {
            $stats = [
                'total_quoted' => ServiceLead::sum('quoted_amount') ?? 0,
                'total_received' => ServiceLead::sum('paid_amount') ?? 0,
                'total_pending' => (ServiceLead::sum('quoted_amount') ?? 0) - (ServiceLead::sum('paid_amount') ?? 0),
                'by_status' => [
                    'pending' => ServiceLead::where('payment_status', 'pending')->count(),
                    'partial' => ServiceLead::where('payment_status', 'partial')->count(),
                    'full' => ServiceLead::where('payment_status', 'full')->count(),
                ],
                'by_type' => [
                    'advance' => ServiceLead::sum('advance_amount') ?? 0,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment analytics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get follow-up analytics
     */
    public function followUps(Request $request): JsonResponse
    {
        try {
            $stats = [
                'total_follow_ups' => ServiceLead::sum('follow_up_count') ?? 0,
                'leads_needing_follow_up' => ServiceLead::needingFollowUp()->count(),
                'average_follow_ups_per_lead' => ServiceLead::count() > 0 ? round((ServiceLead::sum('follow_up_count') ?? 0) / ServiceLead::count(), 2) : 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve follow-up analytics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get priority-based analytics
     */
    public function byPriority(Request $request): JsonResponse
    {
        try {
            $priorities = ['urgent', 'high', 'medium', 'low'];
            $data = [];

            foreach ($priorities as $priority) {
                $leads = ServiceLead::where('priority', $priority);
                $totalLeads = $leads->count();
                $closedLeads = (clone $leads)->closed()->count();

                $data[] = [
                    'priority' => $priority,
                    'total_leads' => $totalLeads,
                    'closed_leads' => $closedLeads,
                    'open_leads' => $totalLeads - $closedLeads,
                    'conversion_rate' => $totalLeads > 0 ? round(($closedLeads / $totalLeads) * 100, 2) : 0,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve priority analytics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get source-based analytics
     */
    public function bySource(Request $request): JsonResponse
    {
        try {
            $sources = ServiceLead::select('lead_source')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(CASE WHEN closed_at IS NOT NULL THEN 1 ELSE 0 END) as closed')
                ->groupBy('lead_source')
                ->where('lead_source', '!=', null)
                ->get()
                ->map(function ($item) {
                    return [
                        'source' => $item->lead_source,
                        'total_leads' => $item->count,
                        'closed_leads' => $item->closed,
                        'conversion_rate' => $item->count > 0 ? round(($item->closed / $item->count) * 100, 2) : 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sources,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve source analytics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get closure analytics
     */
    public function closures(Request $request): JsonResponse
    {
        try {
            $closedLeads = ServiceLead::closed();
            $reasons = $closedLeads->select('closure_reason')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('closure_reason')
                ->get()
                ->map(function ($item) {
                    return [
                        'reason' => $item->closure_reason,
                        'count' => $item->count,
                        'percentage' => ServiceLead::closed()->count() > 0 ? round(($item->count / ServiceLead::closed()->count()) * 100, 2) : 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $reasons,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve closure analytics',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    // Helper methods
    private function calculateConversionRate()
    {
        $total = ServiceLead::count();
        $closed = ServiceLead::closed()->count();

        return $total > 0 ? round(($closed / $total) * 100, 2) : 0;
    }

    private function getPipelineStats()
    {
        $stages = LeadStage::active()->get();
        $pipeline = [];

        foreach ($stages as $stage) {
            $pipeline[] = [
                'stage_name' => $stage->stage_name,
                'lead_count' => $stage->leads()->open()->count(),
            ];
        }

        return $pipeline;
    }

    private function getSalesTeamStats()
    {
        $topSalesPeople = ServiceLead::where('lead_owner_id', '!=', null)
            ->selectRaw('lead_owner_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('lead_owner_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('leadOwner')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->leadOwner->name ?? 'Unknown',
                    'lead_count' => $item->count,
                ];
            });

        return $topSalesPeople;
    }

    private function getPaymentStats()
    {
        return [
            'total_quoted' => ServiceLead::sum('quoted_amount') ?? 0,
            'total_received' => ServiceLead::sum('paid_amount') ?? 0,
            'pending_amount' => (ServiceLead::sum('quoted_amount') ?? 0) - (ServiceLead::sum('paid_amount') ?? 0),
        ];
    }

    private function getFollowUpStats()
    {
        return [
            'total_follow_ups' => ServiceLead::sum('follow_up_count') ?? 0,
            'pending' => ServiceLead::needingFollowUp()->count(),
        ];
    }
}
