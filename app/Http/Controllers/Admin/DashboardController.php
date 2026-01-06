<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sites' => \App\Models\Site::count(),
            'total_team_members' => \App\Models\HrmEmployee::where('status', 'active')->count(),
            'total_news' => \App\Models\NewsMedia::count(),
            'total_careers' => \App\Models\Career::where('is_active', true)->count(),
            'total_case_studies' => \App\Models\CaseStudy::count(),
            'total_blogs' => \App\Models\Blog::count(),
            'new_contact_forms' => \App\Models\ContactForm::where('status', 'new')->count(),
            'new_booking_forms' => \App\Models\BookingForm::where('status', 'new')->count(),
        ];

        // HRM Stats
        $hrmStats = [
            'total_employees' => \App\Models\HrmEmployee::where('status', 'active')->count(),
            'pending_leaves' => \App\Models\HrmLeaveRequest::where('status', 'pending')->count(),
            'draft_payrolls' => \App\Models\HrmPayrollRecord::where('status', 'draft')->count(),
            'approved_payrolls' => \App\Models\HrmPayrollRecord::where('status', 'approved')->count(),
            'paid_payrolls' => \App\Models\HrmPayrollRecord::where('status', 'paid')->count(),
            'unreviewed_anomalies' => \App\Models\HrmAttendanceAnomaly::where('reviewed', false)->count(),
        ];

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

        return view('admin.dashboard', compact('stats', 'hrmStats', 'recentContacts', 'recentBookings', 'pendingLeaves'));
    }
}
