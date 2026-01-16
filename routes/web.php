<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\NewsMediaController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\CaseStudyController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ContactFormController;
use App\Http\Controllers\Admin\BookingFormController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ScheduleMeetingController;
use App\Http\Controllers\Admin\HiringController;
use App\Http\Controllers\Admin\CompanyListController;
use App\Http\Controllers\Admin\HrmDepartmentController;
use App\Http\Controllers\Admin\HrmEmployeeController;
use App\Http\Controllers\Admin\HrmAttendanceController;
use App\Http\Controllers\Admin\HrmHolidayController;
use App\Http\Controllers\Admin\HrmPayrollController;
use App\Http\Controllers\Admin\HrmLeaveController;
use App\Http\Controllers\Admin\HrmLeavePolicyController;
use App\Http\Controllers\Admin\HrmOrganizationController;
use App\Http\Controllers\Admin\HrmResourceRequestController;
use App\Http\Controllers\Admin\HrmExpenseClaimController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\EmployeeHolidayController;
use App\Http\Controllers\Employee\ResourceRequestController as EmployeeResourceRequestController;
use App\Http\Controllers\Employee\ExpenseClaimController as EmployeeExpenseClaimController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\ComplaintController as EmployeeComplaintController;
use App\Http\Controllers\Employee\FeedbackController as EmployeeFeedbackController;
use App\Http\Controllers\Employee\AnnouncementController as EmployeeAnnouncementController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee Self-Service Portal
    Route::prefix('employee')->name('employee.')->middleware('employee')->group(function () {
        Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [EmployeeProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [EmployeeProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [EmployeeProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/avatar', [EmployeeProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
        Route::delete('/profile/avatar', [EmployeeProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Attendance
        Route::get('/attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/data', [EmployeeAttendanceController::class, 'data'])->name('attendance.data');

        // Payroll
        Route::get('/payroll', [EmployeePayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/{id}', [EmployeePayrollController::class, 'show'])->name('payroll.show');
        Route::get('/payroll/{id}/download', [EmployeePayrollController::class, 'downloadPayslip'])->name('payroll.download');
        Route::get('/payroll-data', [EmployeePayrollController::class, 'data'])->name('payroll.data');

        // Leave Requests
        Route::get('/leave', [EmployeeLeaveController::class, 'index'])->name('leave.index');
        Route::get('/leave/create', [EmployeeLeaveController::class, 'create'])->name('leave.create');
        Route::post('/leave', [EmployeeLeaveController::class, 'store'])->name('leave.store');
        Route::get('/leave/{id}', [EmployeeLeaveController::class, 'show'])->name('leave.show');
        Route::post('/leave/{id}/cancel', [EmployeeLeaveController::class, 'cancel'])->name('leave.cancel');
        Route::get('/leave-data', [EmployeeLeaveController::class, 'data'])->name('leave.data');

        // Resource Requests (employee self-service)
        Route::get('/resource-requests', [EmployeeResourceRequestController::class, 'index'])->name('resource-requests.index');
        Route::get('/resource-requests/create', [EmployeeResourceRequestController::class, 'create'])->name('resource-requests.create');
        Route::post('/resource-requests', [EmployeeResourceRequestController::class, 'store'])->name('resource-requests.store');
        Route::get('/resource-requests/{resourceRequest}', [EmployeeResourceRequestController::class, 'show'])->name('resource-requests.show');

        // Expense Claims (employee self-service)
        Route::get('/expense-claims', [EmployeeExpenseClaimController::class, 'index'])->name('expense-claims.index');
        Route::get('/expense-claims/create', [EmployeeExpenseClaimController::class, 'create'])->name('expense-claims.create');
        Route::post('/expense-claims', [EmployeeExpenseClaimController::class, 'store'])->name('expense-claims.store');

        // Holidays
        Route::get('/holidays', [EmployeeHolidayController::class, 'index'])->name('holidays.index');
        // Calendar (merged view: holidays + events)
        Route::get('/calendar', [\App\Http\Controllers\Employee\EmployeeCalendarController::class, 'index'])->name('calendar.index');

        // Complaints/Feedback
        Route::get('/complaints', [EmployeeComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/complaints/create', [EmployeeComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/complaints', [EmployeeComplaintController::class, 'store'])->name('complaints.store');
        Route::get('/complaints/{complaint}', [EmployeeComplaintController::class, 'show'])->name('complaints.show');

        // Weekly Feedback
        Route::get('/feedback', [EmployeeFeedbackController::class, 'dashboard'])->name('feedback.dashboard');
        Route::get('/feedback/create', [EmployeeFeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/feedback', [EmployeeFeedbackController::class, 'store'])->name('feedback.store');
        Route::get('/feedback/history', [EmployeeFeedbackController::class, 'history'])->name('feedback.history');
        Route::get('/feedback/{feedback}', [EmployeeFeedbackController::class, 'show'])->name('feedback.show');

        // Announcements
        Route::get('/announcements', [EmployeeAnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/{announcement}', [EmployeeAnnouncementController::class, 'show'])->name('announcements.show');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/all', [NotificationController::class, 'all'])->name('all');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
            Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        });
    });

    // Admin Routes without workspace
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        // Admin Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::resource('sites', SiteController::class);

        // Site-specific dashboard
        Route::get('sites/{site}/dashboard', [SiteController::class, 'dashboard'])->name('sites.dashboard');

        // Site-specific content management routes (filtered by site query parameter)
        Route::resource('team-members', TeamMemberController::class);
        Route::resource('news-media', NewsMediaController::class);
        Route::resource('careers', CareerController::class);
        Route::resource('case-studies', CaseStudyController::class);
        Route::resource('blogs', BlogController::class);
        Route::resource('services', ServiceController::class);
        Route::resource('hirings', HiringController::class);
        Route::resource('companies-list', CompanyListController::class);
        Route::resource('contact-forms', ContactFormController::class)->only(['index', 'show', 'destroy']);
        Route::resource('booking-forms', BookingFormController::class)->only(['index', 'show', 'destroy']);

        // Schedule Meetings
        Route::get('schedule-meetings', [ScheduleMeetingController::class, 'index'])->name('schedule-meetings.index');
        Route::get('schedule-meetings/{scheduleMeeting}', [ScheduleMeetingController::class, 'show'])->name('schedule-meetings.show');
        Route::patch('schedule-meetings/{scheduleMeeting}/status', [ScheduleMeetingController::class, 'updateStatus'])->name('schedule-meetings.update-status');
        Route::delete('schedule-meetings/{scheduleMeeting}', [ScheduleMeetingController::class, 'destroy'])->name('schedule-meetings.destroy');

        // User Management
        Route::resource('users', UserController::class);
        Route::get('users/create-for-employee/{employee}', [UserController::class, 'createForEmployee'])->name('users.create-for-employee');
        Route::post('users/store-for-employee/{employee}', [UserController::class, 'storeForEmployee'])->name('users.store-for-employee');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('users/{user}/send-reset-link', [UserController::class, 'sendPasswordResetLink'])->name('users.send-reset-link');
        Route::post('users/{user}/set-password', [UserController::class, 'setPassword'])->name('users.set-password');
        Route::post('users/{user}/toggle-leads-access', [UserController::class, 'toggleLeadsAccess'])->name('users.toggle-leads-access');

        // Jibble Employee Management
        Route::get('employees/{employee}/link-jibble', [UserController::class, 'linkJibbleForm'])->name('employees.link-jibble-form');
        Route::post('employees/{employee}/link-jibble', [UserController::class, 'linkJibble'])->name('employees.link-jibble');
        Route::post('employees/{employee}/unlink-jibble', [UserController::class, 'unlinkJibble'])->name('employees.unlink-jibble');
        Route::delete('employees/{employee}/delete-jibble', [UserController::class, 'deleteJibbleEmployee'])->name('employees.delete-jibble');

        // Restore soft-deleted records
        Route::get('users/trash', [UserController::class, 'trash'])->name('users.trash');
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::post('employees/{id}/restore', [UserController::class, 'restoreEmployee'])->name('employees.restore');

        // Leads Management with Permission-based Access Control
        Route::prefix('leads')->name('leads.')->group(function () {
            // Dashboard route
            Route::get('/dashboard', [\App\Http\Controllers\Admin\LeadAnalyticsController::class, 'dashboard'])
                ->middleware('can.manage.leads:view')
                ->name('dashboard');

            // Utility routes (must come before /{lead} routes)
            Route::get('/statuses', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'statuses'])
                ->middleware('can.manage.leads:view')
                ->name('statuses');

            // Analytics routes - admin only (must come before /{lead})
            Route::get('/analytics', [\App\Http\Controllers\Admin\LeadAnalyticsController::class, 'index'])
                ->middleware('can.manage.leads:analytics')
                ->name('analytics');
            Route::get('/export/excel', [\App\Http\Controllers\Admin\LeadAnalyticsController::class, 'exportExcel'])
                ->middleware('can.manage.leads:analytics')
                ->name('export.excel');
            Route::get('/export/pdf', [\App\Http\Controllers\Admin\LeadAnalyticsController::class, 'exportPdf'])
                ->middleware('can.manage.leads:analytics')
                ->name('export.pdf');

            // Create routes - admin only (must come before /{lead})
            Route::get('/create', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'create'])
                ->middleware('can.manage.leads:create')
                ->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'store'])
                ->middleware('can.manage.leads:create')
                ->name('store');

            // View list routes
            Route::get('/', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'index'])
                ->middleware('can.manage.leads:view')
                ->name('index');

            // Dynamic routes (must come last)
            Route::get('/{lead}', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'show'])
                ->middleware('can.manage.leads:view')
                ->name('show');

            Route::get('/{lead}/edit', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'edit'])
                ->middleware('can.manage.leads:edit')
                ->name('edit');
            Route::put('/{lead}', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'update'])
                ->middleware('can.manage.leads:edit')
                ->name('update');

            Route::patch('/{lead}/status', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'updateStatus'])
                ->middleware('can.manage.leads:edit')
                ->name('update-status');

            Route::patch('/{lead}/assign', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'assign'])
                ->middleware('can.manage.leads:assign')
                ->name('assign');

            Route::delete('/{lead}', [\App\Http\Controllers\Admin\ServiceLeadController::class, 'destroy'])
                ->middleware('can.manage.leads:delete')
                ->name('destroy');
        });

        // HRM Module Routes
        Route::prefix('hrm')->name('hrm.')->group(function () {
            // Organization Management (Companies & Departments)
            Route::get('organization', [HrmOrganizationController::class, 'index'])->name('organization.index');

            Route::get('organization/companies/create', [HrmOrganizationController::class, 'create'])->name('organization.companies.create');
            Route::post('organization/companies', [HrmOrganizationController::class, 'store'])->name('organization.companies.store');
            Route::get('organization/companies/{company}', [HrmOrganizationController::class, 'show'])->name('organization.companies.show');
            Route::get('organization/companies/{company}/edit', [HrmOrganizationController::class, 'edit'])->name('organization.companies.edit');
            Route::put('organization/companies/{company}', [HrmOrganizationController::class, 'update'])->name('organization.companies.update');
            Route::delete('organization/companies/{company}', [HrmOrganizationController::class, 'destroy'])->name('organization.companies.destroy');

            // Departments
            Route::resource('departments', HrmDepartmentController::class);

            // Employees
            Route::resource('employees', HrmEmployeeController::class);
            Route::post('employees/bulk-update-salary', [HrmEmployeeController::class, 'bulkUpdateSalary'])->name('employees.bulk-update-salary');
            Route::post('employees/update-individual-salaries', [HrmEmployeeController::class, 'updateIndividualSalaries'])->name('employees.update-individual-salaries');
            // Jibble sync disabled: employees are managed internally and linked to Finance

            // Attendance
            Route::get('attendance', [HrmAttendanceController::class, 'index'])->name('attendance.index');
            Route::get('attendance/calendar', [HrmAttendanceController::class, 'calendar'])->name('attendance.calendar');
            Route::get('attendance/active-users', [HrmAttendanceController::class, 'activeUsers'])->name('attendance.active-users');
            Route::get('attendance/active-users/json', [HrmAttendanceController::class, 'activeUsersJson'])->name('attendance.active-users.json');
            Route::get('attendance/sync', [HrmAttendanceController::class, 'syncForm'])->name('attendance.sync-form');
            Route::post('attendance/sync', [HrmAttendanceController::class, 'syncFromJibble'])->name('attendance.sync');
            Route::get('attendance/sync-employees', [HrmAttendanceController::class, 'syncEmployees'])->name('attendance.sync-employees');
            Route::post('attendance/sync-all', [HrmAttendanceController::class, 'syncAll'])->name('attendance.sync-all');
            Route::get('attendance/employee/{employee}', [HrmAttendanceController::class, 'employee'])->name('attendance.employee');
            Route::get('attendance/{attendance}', [HrmAttendanceController::class, 'show'])->name('attendance.show');

            // Holidays
            Route::resource('holidays', HrmHolidayController::class)->except(['show', 'create']);

            // Payroll Management
            Route::get('payroll', [HrmPayrollController::class, 'index'])->name('payroll.index');
            Route::get('payroll/create', [HrmPayrollController::class, 'create'])->name('payroll.create');
            Route::post('payroll', [HrmPayrollController::class, 'store'])->name('payroll.store');
            Route::get('payroll/{payroll}', [HrmPayrollController::class, 'show'])->name('payroll.show');
            Route::get('payroll/{payroll}/edit', [HrmPayrollController::class, 'edit'])->name('payroll.edit');
            Route::put('payroll/{payroll}', [HrmPayrollController::class, 'update'])->name('payroll.update');
            Route::delete('payroll/{payroll}', [HrmPayrollController::class, 'destroy'])->name('payroll.destroy');
            Route::post('payroll/{payroll}/approve', [HrmPayrollController::class, 'approve'])->name('payroll.approve');
            Route::post('payroll/{payroll}/regenerate', [HrmPayrollController::class, 'regenerate'])->name('payroll.regenerate');
            Route::post('payroll/{payroll}/review-anomalies', [HrmPayrollController::class, 'reviewAnomalies'])->name('payroll.review-anomalies');
            Route::post('payroll/{payroll}/mark-as-paid', [HrmPayrollController::class, 'markAsPaid'])->name('payroll.mark-as-paid');
            Route::post('payroll/{payroll}/mark-as-sent', [HrmPayrollController::class, 'markAsSent'])->name('payroll.mark-as-sent');
            Route::get('payroll/{payroll}/download-pdf', [HrmPayrollController::class, 'downloadPdf'])->name('payroll.download-pdf');

            // Resource Requests
            Route::resource('resource-requests', HrmResourceRequestController::class);
            Route::post('resource-requests/{resourceRequest}/approve', [HrmResourceRequestController::class, 'approve'])->name('resource-requests.approve');
            Route::post('resource-requests/{resourceRequest}/reject', [HrmResourceRequestController::class, 'reject'])->name('resource-requests.reject');
            Route::post('resource-requests/{resourceRequest}/fulfill', [HrmResourceRequestController::class, 'fulfill'])->name('resource-requests.fulfill');

            // Expense Claims
            Route::resource('expense-claims', HrmExpenseClaimController::class);
            Route::post('expense-claims/{expenseClaim}/approve', [HrmExpenseClaimController::class, 'approve'])->name('expense-claims.approve');
            Route::post('expense-claims/{expenseClaim}/reject', [HrmExpenseClaimController::class, 'reject'])->name('expense-claims.reject');
            Route::get('expense-claims/ready-for-payroll', [HrmExpenseClaimController::class, 'getReadyForPayroll'])->name('expense-claims.ready-for-payroll');

            // Leave Management
            Route::get('leaves', [HrmLeaveController::class, 'index'])->name('leaves.index');
            Route::get('leaves/create', [HrmLeaveController::class, 'create'])->name('leaves.create');
            Route::post('leaves', [HrmLeaveController::class, 'store'])->name('leaves.store');
            Route::get('leaves/{leave}', [HrmLeaveController::class, 'show'])->name('leaves.show');
            Route::post('leaves/{leave}/approve', [HrmLeaveController::class, 'approve'])->name('leaves.approve');
            Route::post('leaves/{leave}/reject', [HrmLeaveController::class, 'reject'])->name('leaves.reject');
            Route::post('leaves/{leave}/cancel', [HrmLeaveController::class, 'cancel'])->name('leaves.cancel');

            // Leave Policies
            Route::resource('leave-policies', HrmLeavePolicyController::class);
        });

        // Complaints Management
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [AdminComplaintController::class, 'index'])->name('index');
            Route::get('/{complaint}', [AdminComplaintController::class, 'show'])->name('show');
            Route::patch('/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('update-status');
            Route::patch('/{complaint}/priority', [AdminComplaintController::class, 'updatePriority'])->name('update-priority');
            Route::post('/{complaint}/notes', [AdminComplaintController::class, 'addNotes'])->name('add-notes');
            Route::delete('/{complaint}', [AdminComplaintController::class, 'destroy'])->name('destroy');
        });

        // Weekly Feedback Management
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::get('/', [AdminFeedbackController::class, 'index'])->name('index');
            Route::get('/{feedback}', [AdminFeedbackController::class, 'show'])->name('show');
            Route::post('/{feedback}/notes', [AdminFeedbackController::class, 'addNotes'])->name('add-notes');
            Route::get('/analytics/dashboard', [AdminFeedbackController::class, 'analytics'])->name('analytics');
        });

        // Announcements Management
        Route::resource('announcements', AdminAnnouncementController::class);

        // Finance Module Routes
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('dashboard', function () {
                return view('admin.finance.dashboard');
            })->name('dashboard');

            Route::get('reports', function () {
                return view('admin.finance.reports');
            })->name('reports');

            Route::get('test', function () {
                return view('admin.finance.test');
            })->name('test');

            // Finance CRUD routes
            Route::resource('companies', App\Http\Controllers\Admin\FinanceCompanyController::class);
            Route::resource('accounts', App\Http\Controllers\Admin\FinanceAccountController::class);
            Route::resource('transactions', App\Http\Controllers\Admin\FinanceTransactionController::class);
            Route::resource('sales', App\Http\Controllers\Admin\FinanceSaleController::class);
            Route::resource('purchases', App\Http\Controllers\Admin\FinancePurchaseController::class);
            Route::resource('customers', App\Http\Controllers\Admin\FinanceCustomerController::class);
            Route::resource('vendors', App\Http\Controllers\Admin\FinanceVendorController::class);
            Route::resource('budgets', App\Http\Controllers\Admin\FinanceBudgetController::class);
            Route::resource('recurring-expenses', App\Http\Controllers\Admin\FinanceRecurringExpenseController::class);

            // Customer bulk operations
            Route::post('customers/export', [App\Http\Controllers\Admin\FinanceCustomerController::class, 'export'])->name('customers.export');
            Route::post('customers/bulk-status', [App\Http\Controllers\Admin\FinanceCustomerController::class, 'bulkUpdateStatus'])->name('customers.bulk-status');
            Route::post('customers/bulk-delete', [App\Http\Controllers\Admin\FinanceCustomerController::class, 'bulkDelete'])->name('customers.bulk-delete');

            // Vendor bulk operations
            Route::post('vendors/export', [App\Http\Controllers\Admin\FinanceVendorController::class, 'export'])->name('vendors.export');
            Route::post('vendors/bulk-status', [App\Http\Controllers\Admin\FinanceVendorController::class, 'bulkUpdateStatus'])->name('vendors.bulk-status');
            Route::post('vendors/bulk-delete', [App\Http\Controllers\Admin\FinanceVendorController::class, 'bulkDelete'])->name('vendors.bulk-delete');

            // Document management routes
            Route::post('documents', [App\Http\Controllers\Admin\FinanceDocumentController::class, 'store'])->name('documents.store');
            Route::get('documents/{document}/download', [App\Http\Controllers\Admin\FinanceDocumentController::class, 'download'])->name('documents.download');
            Route::delete('documents/{document}', [App\Http\Controllers\Admin\FinanceDocumentController::class, 'destroy'])->name('documents.destroy');

            // Founder Management Routes
            Route::resource('founders', App\Http\Controllers\Admin\FinanceFounderController::class);
            Route::post('founders/export', [App\Http\Controllers\Admin\FinanceFounderController::class, 'export'])->name('founders.export');

            // Founder Transaction Routes
            Route::resource('founder-transactions', App\Http\Controllers\Admin\FinanceFounderTransactionController::class);
            Route::post('founder-transactions/{transaction}/approve', [App\Http\Controllers\Admin\FinanceFounderTransactionController::class, 'approve'])->name('founder-transactions.approve');
            Route::post('founder-transactions/{transaction}/cancel', [App\Http\Controllers\Admin\FinanceFounderTransactionController::class, 'cancel'])->name('founder-transactions.cancel');
            Route::post('founder-transactions/{transaction}/settle', [App\Http\Controllers\Admin\FinanceFounderTransactionController::class, 'settle'])->name('founder-transactions.settle');
            Route::get('founder-transactions/{transaction}/download', [App\Http\Controllers\Admin\FinanceFounderTransactionController::class, 'download'])->name('founder-transactions.download');

            // Intercompany Loan Routes
            Route::resource('intercompany-loans', App\Http\Controllers\Admin\FinanceIntercompanyLoanController::class);
            Route::post('intercompany-loans/{loan}/approve', [App\Http\Controllers\Admin\FinanceIntercompanyLoanController::class, 'approve'])->name('intercompany-loans.approve');
            Route::post('intercompany-loans/{loan}/record-payment', [App\Http\Controllers\Admin\FinanceIntercompanyLoanController::class, 'recordPayment'])->name('intercompany-loans.record-payment');
            Route::post('intercompany-loans/{loan}/write-off', [App\Http\Controllers\Admin\FinanceIntercompanyLoanController::class, 'writeOff'])->name('intercompany-loans.write-off');

            // Category Management Routes
            Route::resource('categories', App\Http\Controllers\Admin\FinanceCategoryController::class)->except(['show']);

            // Payment Method Routes
            Route::resource('payment-methods', App\Http\Controllers\Admin\FinancePaymentMethodController::class)->except(['show']);

            // Asset Management Routes (Phase 2)
            Route::resource('assets', App\Http\Controllers\Admin\FinanceAssetController::class);
            Route::post('assets/calculate-depreciation', [App\Http\Controllers\Admin\FinanceAssetController::class, 'calculateDepreciation'])->name('assets.calculate-depreciation');
            Route::post('assets/{asset}/dispose', [App\Http\Controllers\Admin\FinanceAssetController::class, 'dispose'])->name('assets.dispose');
            Route::post('assets/{asset}/transfer', [App\Http\Controllers\Admin\FinanceAssetController::class, 'transfer'])->name('assets.transfer');

            // Chart of Accounts Routes (Phase 3)
            Route::resource('chart-of-accounts', App\Http\Controllers\Finance\ChartOfAccountController::class);

            // Journal Entry Routes (Phase 3)
            Route::resource('journal-entries', App\Http\Controllers\Finance\JournalEntryController::class);
            Route::post('journal-entries/{id}/post', [App\Http\Controllers\Finance\JournalEntryController::class, 'post'])->name('journal-entries.post');
            Route::post('journal-entries/{id}/reverse', [App\Http\Controllers\Finance\JournalEntryController::class, 'reverse'])->name('journal-entries.reverse');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
