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
        // Basic stats - simple counts
        $stats = [
            'total_sites' => 0,
            'total_team_members' => 0,
            'total_news' => 0,
            'total_careers' => 0,
            'total_case_studies' => 0,
            'total_blogs' => 0,
            'new_contact_forms' => 0,
            'new_booking_forms' => 0,
        ];

        // HRM Stats
        $hrmStats = [
            'total_employees' => 0,
            'active_employees' => 0,
            'pending_leaves' => 0,
            'draft_payrolls' => 0,
            'approved_payrolls' => 0,
            'paid_payrolls' => 0,
            'unreviewed_anomalies' => 0,
        ];

        // Simple default finance data (no service calls)
        $financeData = [
            'revenue' => 0,
            'expenses' => 0,
            'pending_receivables' => 0,
            'receivables' => 0,
            'payables' => 0,
        ];

        // Empty collections as fallback
        $recentContacts = collect();
        $recentBookings = collect();
        $pendingLeaves = collect();

        // Leads Stats - Initialize with defaults
        $leadsStats = [
            'total_leads' => 0,
            'open_leads' => 0,
            'recent_leads' => collect(),
        ];

        \Log::info('Dashboard: leadsStats initialized', ['leadsStats' => $leadsStats]);

        // Load all stats
        try {
            // Sites
            if (class_exists(\App\Models\Site::class)) {
                $stats['total_sites'] = \App\Models\Site::count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard Site load failed: ' . $e->getMessage());
        }

        try {
            // Team Members
            if (class_exists(\App\Models\TeamMember::class)) {
                $stats['total_team_members'] = \App\Models\TeamMember::count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard TeamMember load failed: ' . $e->getMessage());
        }

        try {
            // News
            if (class_exists(\App\Models\NewsMedia::class)) {
                $stats['total_news'] = \App\Models\NewsMedia::count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard NewsMedia load failed: ' . $e->getMessage());
        }

        try {
            // Careers
            if (class_exists(\App\Models\Career::class)) {
                $stats['total_careers'] = \App\Models\Career::count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard Career load failed: ' . $e->getMessage());
        }

        try {
            // Case Studies
            if (class_exists(\App\Models\CaseStudy::class)) {
                $stats['total_case_studies'] = \App\Models\CaseStudy::count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard CaseStudy load failed: ' . $e->getMessage());
        }

        try {
            // Blogs
            if (class_exists(\App\Models\Blog::class)) {
                $stats['total_blogs'] = \App\Models\Blog::count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard Blog load failed: ' . $e->getMessage());
        }

        try {
            // Contact Forms
            if (class_exists(\App\Models\ContactForm::class)) {
                $stats['new_contact_forms'] = \App\Models\ContactForm::where('status', 'new')->count();
                $recentContacts = \App\Models\ContactForm::with('site')->latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard ContactForm load failed: ' . $e->getMessage());
        }

        try {
            // Booking Forms
            if (class_exists(\App\Models\BookingForm::class)) {
                $stats['new_booking_forms'] = \App\Models\BookingForm::where('status', 'new')->count();
                $recentBookings = \App\Models\BookingForm::with('site')->latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard BookingForm load failed: ' . $e->getMessage());
        }

        try {
            // HRM Employees
            if (class_exists(\App\Models\HrmEmployee::class)) {
                $hrmStats['total_employees'] = \App\Models\HrmEmployee::count();
                $hrmStats['active_employees'] = \App\Models\HrmEmployee::where('status', 'active')->count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard HrmEmployee load failed: ' . $e->getMessage());
        }

        try {
            // Leave Requests
            if (class_exists(\App\Models\HrmLeaveRequest::class)) {
                $hrmStats['pending_leaves'] = \App\Models\HrmLeaveRequest::where('status', 'pending')->count();
                $pendingLeaves = \App\Models\HrmLeaveRequest::with('employee')->where('status', 'pending')->latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard HrmLeaveRequest load failed: ' . $e->getMessage());
        }

        try {
            // Payroll
            if (class_exists(\App\Models\HrmPayroll::class)) {
                $hrmStats['draft_payrolls'] = \App\Models\HrmPayroll::where('status', 'draft')->count();
                $hrmStats['approved_payrolls'] = \App\Models\HrmPayroll::where('status', 'approved')->count();
                $hrmStats['paid_payrolls'] = \App\Models\HrmPayroll::where('status', 'paid')->count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard HrmPayroll load failed: ' . $e->getMessage());
        }

        try {
            // Attendance Anomalies
            if (class_exists(\App\Models\HrmAttendanceAnomaly::class)) {
                $hrmStats['unreviewed_anomalies'] = \App\Models\HrmAttendanceAnomaly::where('reviewed', false)->count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard HrmAttendanceAnomaly load failed: ' . $e->getMessage());
        }

        try {
            // Service Leads
            if (class_exists(\App\Models\ServiceLead::class)) {
                $leadsStats['total_leads'] = \App\Models\ServiceLead::count();
                $leadsStats['open_leads'] = \App\Models\ServiceLead::where('status', 'active')->count();
                $leadsStats['recent_leads'] = \App\Models\ServiceLead::with(['leadStage', 'leadOwner'])
                    ->latest()
                    ->take(5)
                    ->get();

                \Log::info('Leads loaded successfully', [
                    'total_leads' => $leadsStats['total_leads'],
                    'open_leads' => $leadsStats['open_leads'],
                    'recent_leads_count' => $leadsStats['recent_leads']->count()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard ServiceLead load failed: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

        \Log::info('Before return - leadsStats value', ['leadsStats' => $leadsStats]);

        return view('admin.dashboard', compact('stats', 'hrmStats', 'financeData', 'recentContacts', 'recentBookings', 'pendingLeaves', 'leadsStats'));
    }
}
