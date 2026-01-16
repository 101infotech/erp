<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Constants\PermissionConstants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view
     * Redirects users to appropriate dashboard based on role
     */
    public function index()
    {
        $user = Auth::user();

        // Redirect users based on role hierarchy
        if ($user->hasRole(['super_admin', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }

        // Redirect to employee dashboard for staff
        return redirect()->route('employee.dashboard');
    }

    /**
     * Display the admin dashboard
     */
    public function admin()
    {
        $user = Auth::user();

        // Get user permissions for module visibility
        $moduleAccess = $this->getUserModuleAccess($user);

        $stats = [
            'total_sites' => \App\Models\Site::count(),
            'total_team_members' => \App\Models\TeamMember::count(),
            'total_blogs' => \App\Models\Blog::count(),
            'new_contact_forms' => \App\Models\ContactForm::whereDate('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentContacts = \App\Models\ContactForm::with('site')
            ->latest()
            ->limit(5)
            ->get();

        $recentBookings = \App\Models\BookingForm::with('site')
            ->latest()
            ->limit(5)
            ->get();

        // Get finance data if user has permission
        $financeData = [];
        if ($user->hasPermission(PermissionConstants::VIEW_FINANCES)) {
            $financeData = $this->getFinanceData();
        }

        // Get HRM stats if user has permission
        $hrmStats = [];
        if ($user->hasPermission(PermissionConstants::VIEW_EMPLOYEES)) {
            $hrmStats = $this->getHrmStats();
        }

        // Get pending leaves if user has permission
        $pendingLeaves = collect();
        if ($user->hasPermission(PermissionConstants::APPROVE_LEAVE_REQUESTS)) {
            $pendingLeaves = $this->getPendingLeaves();
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentContacts',
            'recentBookings',
            'financeData',
            'hrmStats',
            'pendingLeaves',
            'moduleAccess'
        ));
    }

    /**
     * Get user's accessible modules based on permissions
     */
    private function getUserModuleAccess($user)
    {
        return [
            'leads' => $user->hasPermission(PermissionConstants::VIEW_LEADS),
            'finance' => $user->hasPermission(PermissionConstants::VIEW_FINANCES),
            'hrm' => $user->hasPermission(PermissionConstants::VIEW_EMPLOYEES),
            'projects' => $user->hasPermission(PermissionConstants::VIEW_PROJECTS),
            'admin' => $user->hasPermission(PermissionConstants::MANAGE_USERS),
        ];
    }

    /**
     * Get finance dashboard data
     */
    private function getFinanceData()
    {
        try {
            $totalRevenue = \App\Models\FinanceTransaction::where('type', 'income')->sum('amount');
            $totalExpenses = \App\Models\FinanceTransaction::where('type', 'expense')->sum('amount');
            $pendingReceivables = \App\Models\FinanceTransaction::where('status', 'pending')
                ->where('type', 'income')
                ->sum('amount');

            return [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'pending_receivables' => $pendingReceivables,
                'net_profit' => $totalRevenue - $totalExpenses,
            ];
        } catch (\Exception $e) {
            return [
                'total_revenue' => 0,
                'total_expenses' => 0,
                'pending_receivables' => 0,
                'net_profit' => 0,
            ];
        }
    }

    /**
     * Get HRM statistics
     */
    private function getHrmStats()
    {
        try {
            $totalEmployees = \App\Models\HrmEmployee::count();
            $activeEmployees = \App\Models\HrmEmployee::where('status', 'active')->count();
            $totalDepartments = \App\Models\HrmDepartment::count();
            $onLeaveToday = \App\Models\HrmLeaveRequest::where('from_date', '<=', now()->toDateString())
                ->where('to_date', '>=', now()->toDateString())
                ->where('status', 'approved')
                ->count();

            return [
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
                'total_departments' => $totalDepartments,
                'on_leave_today' => $onLeaveToday,
            ];
        } catch (\Exception $e) {
            return [
                'total_employees' => 0,
                'active_employees' => 0,
                'total_departments' => 0,
                'on_leave_today' => 0,
            ];
        }
    }

    /**
     * Get pending leave requests
     */
    private function getPendingLeaves()
    {
        try {
            return \App\Models\HrmLeaveRequest::where('status', 'pending')
                ->with('employee')
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get dashboard statistics
     */
    public function stats()
    {
        $totalBudget = Project::sum('budget');
        $completedTasks = Project::sum('completed_tasks');

        // Calculate weekly change (comparing with last week)
        $lastWeekBudget = Project::where('updated_at', '>=', Carbon::now()->subWeek())
            ->sum('budget');
        $budgetChangeWeek = $lastWeekBudget > 0
            ? round((($totalBudget - $lastWeekBudget) / $lastWeekBudget) * 100, 2)
            : 0;

        // Tasks completed today
        $tasksCompletedToday = Project::whereDate('updated_at', Carbon::today())
            ->sum('completed_tasks');

        // Average tasks value (total budget / total tasks)
        $totalTasks = Project::sum('total_tasks');
        $averageTasksValue = $totalTasks > 0 ? round($totalBudget / $totalTasks, 2) : 0;

        // Average tasks per project
        $projectCount = Project::count();
        $averageTasksPerProject = $projectCount > 0
            ? round($totalTasks / $projectCount, 1)
            : 0;

        // New projects this year
        $newProjectsCount = Project::whereYear('created_at', Carbon::now()->year)
            ->count();

        return response()->json([
            'total_budget' => $totalBudget,
            'budget_change_week' => $budgetChangeWeek,
            'total_completed_tasks' => $completedTasks,
            'tasks_completed_today' => $tasksCompletedToday,
            'average_tasks_value' => $averageTasksValue,
            'average_tasks_per_project' => $averageTasksPerProject,
            'new_projects_count' => $newProjectsCount,
        ]);
    }

    /**
     * Get all projects for the dashboard
     */
    public function projects()
    {
        $projects = Project::with(['members' => function ($query) {
            $query->select('users.id', 'users.name', 'users.email')
                ->limit(3); // Only get first 3 members for display
        }])
            ->where('status', 'active')
            ->orderBy('budget', 'desc')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'category' => $project->category,
                    'category_color' => $project->category_color,
                    'completed_tasks' => $project->completed_tasks,
                    'total_tasks' => $project->total_tasks,
                    'budget' => $project->budget,
                    'progress_percentage' => $project->progress_percentage,
                    'team_members' => $project->members->map(function ($member) {
                        return [
                            'id' => $member->id,
                            'name' => $member->name,
                            'avatar' => $member->email ? 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=random' : null,
                        ];
                    }),
                    'member_count' => $project->member_count,
                ];
            });

        return response()->json($projects);
    }

    /**
     * Get calendar events for a specific month
     */
    public function calendar(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $events = CalendarEvent::with('project')
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($event) {
                return $event->event_date->format('Y-m-d');
            })
            ->map(function ($dayEvents) {
                return $dayEvents->map(function ($event) {
                    $teamMembers = [];
                    if ($event->team_members) {
                        $users = User::whereIn('id', $event->team_members)
                            ->select('id', 'name', 'email')
                            ->get();
                        $teamMembers = $users->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
                            ];
                        });
                    }

                    return [
                        'id' => $event->id,
                        'project_id' => $event->project_id,
                        'project_name' => $event->project ? $event->project->name : null,
                        'project_color' => $event->project ? $event->project->category_color : '#4169E1',
                        'title' => $event->title,
                        'priority' => $event->priority,
                        'team_members' => $teamMembers,
                    ];
                })->toArray();
            });

        return response()->json($events);
    }

    /**
     * Get yearly profit data
     */
    public function yearlyProfit(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        $monthlyData = Project::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(budget) as total_budget'),
            DB::raw('SUM(completed_tasks) as total_tasks')
        )
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->month => [
                    'month' => Carbon::create()->month($item->month)->format('M'),
                    'value' => $item->total_budget,
                    'tasks' => $item->total_tasks,
                ]];
            });

        // Fill in missing months with zeros
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            if (isset($monthlyData[$i])) {
                $result[] = $monthlyData[$i];
            } else {
                $result[] = [
                    'month' => Carbon::create()->month($i)->format('M'),
                    'value' => 0,
                    'tasks' => 0,
                ];
            }
        }

        return response()->json($result);
    }
}
