<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountDeletedNotification;
use App\Notifications\EmployeeLinkedNotification;
use App\Notifications\EmployeeUnlinkedNotification;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Fetch HRM Employees instead of Users to show all staff including those without email/user accounts
        // Exclude admin accounts from the list
        $query = \App\Models\HrmEmployee::with('user', 'company', 'department')
            ->where(function ($q) {
                $q->whereDoesntHave('user', function ($userQuery) {
                    $userQuery->where('role', 'admin');
                })
                    ->orWhereNull('user_id');
            })
            ->where('status', 'active')
            ->orderBy('name');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Role filter - filter by user role if exists (only non-admin roles)
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Company filter
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $employees = $query->paginate(20);
        $companies = \App\Models\HrmCompany::orderBy('name')->get();

        // Pass employees as 'users' variable to avoid breaking the view
        return view('admin.users.index', compact('employees', 'companies'))->with('users', $employees);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:user,admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        // Get Jibble employee data if exists
        $jibbleEmployee = \App\Models\HrmEmployee::where('email', $user->email)->first();

        // Get recent attendance if Jibble employee exists
        $recentAttendance = null;
        if ($jibbleEmployee) {
            $recentAttendance = \App\Models\HrmAttendanceDay::where('employee_id', $jibbleEmployee->id)
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();
        }

        return view('admin.users.show', compact('user', 'jibbleEmployee', 'recentAttendance'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:user,admin'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            Log::warning('User attempted to delete their own account', [
                'user_id' => $user->id,
            ]);

            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        return DB::transaction(function () use ($user) {
            // Check if user has an employee record
            $employee = $user->hrmEmployee;
            $hadEmployee = $employee !== null;
            $employeeId = $employee?->id;
            $employeeName = $employee?->name;
            $hadJibbleId = $employee?->jibble_person_id !== null;

            $name = $user->name;
            $email = $user->email;
            $role = $user->role;
            $userId = $user->id;

            // Send notification before deletion
            if ($email) {
                try {
                    $user->notify(new AccountDeletedNotification($name, $email, $hadEmployee));
                } catch (\Exception $e) {
                    Log::warning('Failed to send account deletion notification', [
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Delete user account first
            $user->delete();

            // Delete the employee record if it exists
            if ($employee) {
                // Delete all related attendance records
                \App\Models\HrmAttendanceDay::where('employee_id', $employee->id)->delete();
                \App\Models\HrmAttendanceAnomaly::where('employee_id', $employee->id)->delete();

                // Delete the employee
                $employee->delete();
            }

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'user_id' => $userId,
                    'user_name' => $name,
                    'user_email' => $email,
                    'user_role' => $role,
                    'had_employee_record' => $hadEmployee,
                    'employee_id' => $employeeId,
                    'employee_name' => $employeeName,
                    'had_jibble_id' => $hadJibbleId,
                    'employee_deleted' => $hadEmployee,
                ])
                ->log('User account and employee record deleted');

            Log::info('User account and employee record deleted', [
                'user_id' => $userId,
                'user_name' => $name,
                'user_email' => $email,
                'user_role' => $role,
                'had_employee_record' => $hadEmployee,
                'employee_id' => $employeeId,
                'employee_name' => $employeeName,
                'had_jibble_id' => $hadJibbleId,
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$name}' has been deleted successfully." . ($hadEmployee ? " The employee record has been preserved and unlinked." : ""));
        });
    }

    public function resetPassword(User $user)
    {
        // Generate a strong random password (16 characters with mix of letters, numbers, and symbols)
        $newPassword = Str::password(16, true, true, true, false);
        $user->password = Hash::make($newPassword);
        $user->save();

        try {
            // Send email notification
            $user->notify(new PasswordResetNotification($newPassword));

            return redirect()->back()
                ->with('success', 'Password has been reset and email sent to ' . $user->email . '. The user should check their inbox.');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Password was reset but email failed to send. Please contact the user directly. Error: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset link to user's email
     */
    public function sendPasswordResetLink(User $user)
    {
        try {
            // Send password reset link
            $status = Password::sendResetLink(
                ['email' => $user->email]
            );

            if ($status === Password::RESET_LINK_SENT) {
                return redirect()->back()
                    ->with('success', 'Password reset link has been sent to ' . $user->email);
            }

            return redirect()->back()
                ->with('error', 'Failed to send password reset link. Please try again.');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset link', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send password reset link: ' . $e->getMessage());
        }
    }

    /**
     * Set a new password manually (admin sets the password)
     */
    public function setPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'notify_user' => ['sometimes', 'boolean'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        // Optionally notify the user
        if ($request->has('notify_user') && $request->notify_user) {
            try {
                $user->notify(new \App\Notifications\PasswordChangedNotification());
                return redirect()->back()
                    ->with('success', 'Password has been updated and notification sent to ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send password change notification', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);

                return redirect()->back()
                    ->with('warning', 'Password has been updated but notification failed to send.');
            }
        }

        return redirect()->back()
            ->with('success', 'Password has been updated successfully.');
    }

    /**
     * Toggle leads module access for a user (employee only)
     */
    public function toggleLeadsAccess(User $user)
    {
        // Only employees can have their leads access toggled
        if ($user->role !== 'employee') {
            return redirect()->back()
                ->with('error', 'Leads access can only be toggled for employees.');
        }

        $user->can_access_leads = !$user->can_access_leads;
        $user->save();

        $status = $user->can_access_leads ? 'enabled' : 'disabled';

        return redirect()->back()
            ->with('success', "Leads module access has been {$status} for {$user->name}.");
    }

    /**
     * Show form to link employee with Jibble
     */
    public function linkJibbleForm($employeeId)
    {
        $employee = \App\Models\HrmEmployee::findOrFail($employeeId);
        $availableJibbleEmployees = \App\Models\HrmEmployee::whereNull('user_id')
            ->orWhere('id', $employee->id)
            ->orderBy('name')
            ->get();

        return view('admin.users.link-jibble', compact('employee', 'availableJibbleEmployees'));
    }

    /**
     * Link a user account with a Jibble employee
     */
    public function linkJibble(Request $request, $employeeId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        return DB::transaction(function () use ($validated, $employeeId) {
            $employee = \App\Models\HrmEmployee::findOrFail($employeeId);
            $user = User::findOrFail($validated['user_id']);

            // Check if user is already linked to another employee
            $existingLink = \App\Models\HrmEmployee::where('user_id', $user->id)
                ->where('id', '!=', $employee->id)
                ->first();

            if ($existingLink) {
                Log::warning('Attempted to link user already linked to another employee', [
                    'employee_id' => $employee->id,
                    'user_id' => $user->id,
                    'existing_employee_id' => $existingLink->id,
                    'admin_id' => auth()->id(),
                ]);

                return redirect()->back()
                    ->with('error', "User {$user->name} is already linked to employee {$existingLink->name}. Please unlink first.");
            }

            // Link the employee to the user
            $employee->user_id = $user->id;
            $employee->save();

            // Log activity
            activity()
                ->performedOn($employee)
                ->causedBy(auth()->user())
                ->withProperties([
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'employee_name' => $employee->name,
                ])
                ->log('Employee linked to user account');

            Log::info('Employee linked to user account', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
            ]);

            // Send email notification
            if ($user->email) {
                try {
                    $user->notify(new EmployeeLinkedNotification($employee));
                } catch (\Exception $e) {
                    Log::warning('Failed to send employee linked notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->route('admin.users.index')
                ->with('success', "Successfully linked {$employee->name} with user account {$user->email}");
        });
    }

    /**
     * Unlink user account from Jibble employee
     */
    public function unlinkJibble($employeeId)
    {
        return DB::transaction(function () use ($employeeId) {
            $employee = \App\Models\HrmEmployee::findOrFail($employeeId);

            if (!$employee->user_id) {
                Log::warning('Attempted to unlink employee not linked to any user', [
                    'employee_id' => $employee->id,
                    'admin_id' => auth()->id(),
                ]);

                return redirect()->back()
                    ->with('error', 'This employee is not linked to any user account.');
            }

            $previousUserId = $employee->user_id;
            $previousUserEmail = $employee->user->email ?? 'N/A';
            $previousUser = $employee->user;

            $employee->user_id = null;
            $employee->save();

            // Log activity
            activity()
                ->performedOn($employee)
                ->causedBy(auth()->user())
                ->withProperties([
                    'previous_user_id' => $previousUserId,
                    'previous_user_email' => $previousUserEmail,
                ])
                ->log('Employee unlinked from user account');

            Log::info('Employee unlinked from user account', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'previous_user_id' => $previousUserId,
                'previous_user_email' => $previousUserEmail,
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
            ]);

            // Send email notification
            if ($previousUser && $previousUser->email) {
                try {
                    $previousUser->notify(new EmployeeUnlinkedNotification($employee->name));
                } catch (\Exception $e) {
                    Log::warning('Failed to send employee unlinked notification', [
                        'user_id' => $previousUserId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->back()
                ->with('success', "Successfully unlinked user account from {$employee->name}");
        });
    }

    /**
     * Delete a Jibble-synced employee
     */
    public function deleteJibbleEmployee($employeeId)
    {
        return DB::transaction(function () use ($employeeId) {
            $employee = \App\Models\HrmEmployee::findOrFail($employeeId);

            // Check if employee has a Jibble ID (was synced from Jibble)
            if (!$employee->jibble_person_id) {
                Log::warning('Attempted to delete non-Jibble employee via Jibble delete method', [
                    'employee_id' => $employee->id,
                    'admin_id' => auth()->id(),
                ]);

                return redirect()->back()
                    ->with('error', 'This employee was not synced from Jibble and cannot be deleted this way.');
            }

            $name = $employee->name;
            $attendanceCount = $employee->attendanceDays()->count();
            $hadUser = $employee->user_id !== null;
            $userId = $employee->user_id;

            // Delete related records first
            $employee->attendanceDays()->delete();

            // If employee has user account, optionally delete it too
            if ($employee->user_id) {
                $user = $employee->user;
                $employee->user_id = null;
                $employee->save();

                // You can uncomment this if you want to delete the user account too
                // $user->delete();
            }

            // Delete the employee
            $employee->delete();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'employee_id' => $employeeId,
                    'employee_name' => $name,
                    'jibble_person_id' => $employee->jibble_person_id,
                    'had_user_account' => $hadUser,
                    'user_id' => $userId,
                    'attendance_records_deleted' => $attendanceCount,
                ])
                ->log('Jibble employee deleted');

            Log::info('Jibble employee deleted', [
                'employee_id' => $employeeId,
                'employee_name' => $name,
                'jibble_person_id' => $employee->jibble_person_id,
                'had_user_account' => $hadUser,
                'user_id' => $userId,
                'attendance_records_deleted' => $attendanceCount,
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "Successfully deleted Jibble employee: {$name}");
        });
    }

    /**
     * Show trash (soft-deleted records)
     */
    public function trash()
    {
        $deletedUsers = User::onlyTrashed()->latest('deleted_at')->get();
        $deletedEmployees = \App\Models\HrmEmployee::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.users.trash', compact('deletedUsers', 'deletedEmployees'));
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore($id)
    {
        return DB::transaction(function () use ($id) {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ])
                ->log('User account restored');

            Log::info('User account restored', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
            ]);

            return redirect()->back()
                ->with('success', "User '{$user->name}' has been restored successfully.");
        });
    }

    /**
     * Restore a soft-deleted employee
     */
    public function restoreEmployee($id)
    {
        return DB::transaction(function () use ($id) {
            $employee = \App\Models\HrmEmployee::onlyTrashed()->findOrFail($id);
            $employee->restore();

            // Log activity
            activity()
                ->performedOn($employee)
                ->causedBy(auth()->user())
                ->withProperties([
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                ])
                ->log('Employee record restored');

            Log::info('Employee record restored', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
            ]);

            return redirect()->back()
                ->with('success', "Employee '{$employee->name}' has been restored successfully.");
        });
    }

    /**
     * Show form to create user account for an employee
     */
    public function createForEmployee($employeeId)
    {
        $employee = \App\Models\HrmEmployee::findOrFail($employeeId);

        // Check if employee already has a user account
        if ($employee->user_id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'This employee already has a user account.');
        }

        return view('admin.users.create-for-employee', compact('employee'));
    }

    /**
     * Store user account for an employee
     */
    public function storeForEmployee(Request $request, $employeeId)
    {
        $employee = \App\Models\HrmEmployee::findOrFail($employeeId);

        // Check if employee already has a user account
        if ($employee->user_id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'This employee already has a user account.');
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:user,admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'send_credentials' => ['sometimes', 'boolean'],
        ]);

        return DB::transaction(function () use ($validated, $employee, $request) {
            // Create user account
            $user = User::create([
                'name' => $employee->name,
                'email' => $validated['email'],
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
            ]);

            // Link employee to user
            $employee->user_id = $user->id;
            $employee->email = $validated['email']; // Update employee email if it was empty
            $employee->save();

            // Log activity
            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties([
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                ])
                ->log('User account created for employee');

            Log::info('User account created for employee', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'admin_id' => auth()->id(),
            ]);

            // Send credentials if requested
            if ($request->has('send_credentials') && $request->send_credentials) {
                try {
                    $user->notify(new \App\Notifications\AccountCreatedNotification($validated['password']));
                } catch (\Exception $e) {
                    Log::error('Failed to send account creation notification', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->route('admin.users.index')
                ->with('success', "User account created successfully for {$employee->name}.");
        });
    }
}
