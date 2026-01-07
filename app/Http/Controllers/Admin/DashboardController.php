<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceDashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected FinanceDashboardService $financeDashboardService;

    public function __construct(FinanceDashboardService $financeDashboardService)
    {
        $this->financeDashboardService = $financeDashboardService;
    }

    public function index()
    {
        $stats = [
            'total_sites' => \App\Models\Site::count(),
            'total_team_members' => \App\Models\HrmEmployee::count(),
            'total_news' => \App\Models\NewsMedia::count(),
            'total_careers' => \App\Models\Career::where('is_active', true)->count(),
            'total_case_studies' => \App\Models\CaseStudy::count(),
            'total_blogs' => \App\Models\Blog::count(),
            'new_contact_forms' => \App\Models\ContactForm::where('status', 'new')->count(),
            'new_booking_forms' => \App\Models\BookingForm::where('status', 'new')->count(),
        ];

        // HRM Stats
        $hrmStats = [
            'total_employees' => \App\Models\HrmEmployee::count(),
            'active_employees' => \App\Models\HrmEmployee::where('status', 'active')->count(),
            'pending_leaves' => \App\Models\HrmLeaveRequest::where('status', 'pending')->count(),
            'draft_payrolls' => \App\Models\HrmPayrollRecord::where('status', 'draft')->count(),
            'approved_payrolls' => \App\Models\HrmPayrollRecord::where('status', 'approved')->count(),
            'paid_payrolls' => \App\Models\HrmPayrollRecord::where('status', 'paid')->count(),
            'unreviewed_anomalies' => \App\Models\HrmAttendanceAnomaly::where('reviewed', false)->count(),
        ];

        // Finance Data - fetch server-side to avoid auth issues
        $financeData = null;
        try {
            $companyId = 1; // Default to first company
            $fiscalYear = '2081'; // Current BS fiscal year

            // Check if company exists
            if (\App\Models\FinanceCompany::where('id', $companyId)->exists()) {
                $financeData = $this->financeDashboardService->getDashboardData($companyId, $fiscalYear);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch finance dashboard data: ' . $e->getMessage());
            $financeData = null;
        }

        $recentContacts = \App\Models\ContactForm::with('site')
            ->latest()
            ->take(5)
            ->get();

        $recentBookings = \App\Models\BookingForm::with('site')
            ->latest()
            ->take(5)
            ->get();

        // Recent pending leaves
        $pendingLeaves = \App\Models\HrmLeaveRequest::with('employee')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'hrmStats', 'financeData', 'recentContacts', 'recentBookings', 'pendingLeaves'));
    }
}
