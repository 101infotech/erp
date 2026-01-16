<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceLead;
use App\Models\LeadStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadAnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function dashboard(Request $request)
    {
        $analytics = $this->getAnalyticsData($request);

        return view('admin.leads.dashboard', ['analytics' => $analytics]);
    }

    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $analytics = $this->getAnalyticsData($request);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $analytics,
            ]);
        }

        return view('admin.leads.analytics', ['analytics' => $analytics]);
    }

    /**
     * Get analytics data
     */
    public function getAnalyticsData(Request $request)
    {
        // Summary statistics (all time for overview)
        $totalLeads = ServiceLead::count();
        $activeLeads = ServiceLead::where('status', 'active')->count();
        
        // Count leads that have been converted (booking confirmed or paid)
        $positiveClients = ServiceLead::where(function($query) {
            $query->whereNotNull('booking_confirmed_at')
                  ->orWhere('payment_status', 'paid')
                  ->orWhere('payment_status', 'partial');
        })->count();

        $conversionRate = $totalLeads > 0 ? round(($positiveClients / $totalLeads) * 100, 2) : 0;

        // Revenue metrics - use quoted_amount instead of inspection_charge
        $totalRevenue = ServiceLead::whereNotNull('quoted_amount')->sum('quoted_amount') ?? 0;
        $paidInspections = ServiceLead::whereNotNull('quoted_amount')->where('quoted_amount', '>', 0)->count();
        $averageCharge = $paidInspections > 0 ? round($totalRevenue / $paidInspections, 2) : 0;
        $highestCharge = ServiceLead::max('quoted_amount') ?? 0;

        // Status distribution
        $statusDistribution = ServiceLead::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Service type distribution
        $services = ServiceLead::select('service_requested', DB::raw('count(*) as count'))
            ->groupBy('service_requested')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrends = ServiceLead::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month_num'),
            DB::raw('count(*) as count'),
            DB::raw('SUM(CASE WHEN booking_confirmed_at IS NOT NULL OR payment_status IN ("paid", "partial") THEN 1 ELSE 0 END) as completed')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'), 'asc')
            ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
            ->get()
            ->map(function ($row) {
                $row->month = Carbon::create((int) $row->year, (int) $row->month_num, 1)->format('M Y');
                return $row;
            });

        // Staff performance - use site_visit_assigned_to_id instead
        $staffPerformance = ServiceLead::select(
            'site_visit_assigned_to_id',
            DB::raw('count(*) as total_leads'),
            DB::raw('SUM(CASE WHEN booking_confirmed_at IS NOT NULL OR payment_status IN ("paid", "partial") THEN 1 ELSE 0 END) as completed_leads')
        )
            ->whereNotNull('site_visit_assigned_to_id')
            ->groupBy('site_visit_assigned_to_id')
            ->get()
            ->map(function ($item) {
                $user = \App\Models\User::find($item->site_visit_assigned_to_id);
                return (object) [
                    'assigned_to_name' => $user->name ?? 'Unknown',
                    'total_leads' => $item->total_leads,
                    'completed_leads' => $item->completed_leads,
                ];
            });

        // Recent leads
        $recentLeads = ServiceLead::with(['leadStage', 'leadOwner'])
            ->latest()
            ->take(10)
            ->get();

        return [
            'total_leads' => $totalLeads,
            'active_leads' => $activeLeads,
            'positive_clients' => $positiveClients,
            'conversion_rate' => $conversionRate,
            'total_revenue' => $totalRevenue,
            'average_charge' => $averageCharge,
            'highest_charge' => $highestCharge,
            'paid_inspections' => $paidInspections,
            'status_distribution' => $statusDistribution,
            'services' => $services,
            'monthly_trends' => $monthlyTrends,
            'staff_performance' => $staffPerformance,
            'recent_leads' => $recentLeads,
        ];
    }

    /**
     * Export leads to Excel
     */
    public function exportExcel(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Export functionality not implemented yet',
        ], 501);
    }

    /**
     * Export leads to PDF
     */
    public function exportPdf(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Export functionality not implemented yet',
        ], 501);
    }
}
