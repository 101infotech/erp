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

        // Try to load stats but don't block if it fails
        try {
            if (class_exists(\App\Models\ContactForm::class)) {
                $stats['new_contact_forms'] = \App\Models\ContactForm::where('status', 'new')->count();
                $recentContacts = \App\Models\ContactForm::with('site')->latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard ContactForm load failed: ' . $e->getMessage());
        }

        try {
            if (class_exists(\App\Models\BookingForm::class)) {
                $stats['new_booking_forms'] = \App\Models\BookingForm::where('status', 'new')->count();
                $recentBookings = \App\Models\BookingForm::with('site')->latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard BookingForm load failed: ' . $e->getMessage());
        }

        try {
            if (class_exists(\App\Models\HrmEmployee::class)) {
                $hrmStats['total_employees'] = \App\Models\HrmEmployee::count();
                $hrmStats['active_employees'] = \App\Models\HrmEmployee::where('status', 'active')->count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard HrmEmployee load failed: ' . $e->getMessage());
        }

        try {
            if (class_exists(\App\Models\HrmLeaveRequest::class)) {
                $hrmStats['pending_leaves'] = \App\Models\HrmLeaveRequest::where('status', 'pending')->count();
                $pendingLeaves = \App\Models\HrmLeaveRequest::with('employee')->where('status', 'pending')->latest()->take(5)->get();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard HrmLeaveRequest load failed: ' . $e->getMessage());
        }

        try {
            if (class_exists(\App\Models\Blog::class)) {
                $stats['total_blogs'] = \App\Models\Blog::count();
            }
        } catch (\Exception $e) {
            \Log::warning('Dashboard Blog load failed: ' . $e->getMessage());
        }

        return view('admin.dashboard', compact('stats', 'hrmStats', 'financeData', 'recentContacts', 'recentBookings', 'pendingLeaves'));
    }
}
