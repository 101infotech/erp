<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $query = \App\Models\HrmEmployee::with('user', 'company', 'department')->latest();

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

        // Role filter - filter by user role if exists
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Company filter
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $employees = $query->paginate(15);
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
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Check if user has an employee record
        $employee = $user->hrmEmployee;
        if ($employee) {
            // Unlink the employee from user account before deleting user
            $employee->user_id = null;
            $employee->save();
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' has been deleted successfully." . ($employee ? " The employee record has been preserved and unlinked." : ""));
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

        $employee = \App\Models\HrmEmployee::findOrFail($employeeId);
        $user = User::findOrFail($validated['user_id']);

        // Check if user is already linked to another employee
        $existingLink = \App\Models\HrmEmployee::where('user_id', $user->id)
            ->where('id', '!=', $employee->id)
            ->first();

        if ($existingLink) {
            return redirect()->back()
                ->with('error', "User {$user->name} is already linked to employee {$existingLink->name}. Please unlink first.");
        }

        // Link the employee to the user
        $employee->user_id = $user->id;
        $employee->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Successfully linked {$employee->name} with user account {$user->email}");
    }

    /**
     * Unlink user account from Jibble employee
     */
    public function unlinkJibble($employeeId)
    {
        $employee = \App\Models\HrmEmployee::findOrFail($employeeId);
        
        if (!$employee->user_id) {
            return redirect()->back()
                ->with('error', 'This employee is not linked to any user account.');
        }

        $employee->user_id = null;
        $employee->save();

        return redirect()->back()
            ->with('success', "Successfully unlinked user account from {$employee->name}");
    }

    /**
     * Delete a Jibble-synced employee
     */
    public function deleteJibbleEmployee($employeeId)
    {
        $employee = \App\Models\HrmEmployee::findOrFail($employeeId);

        // Check if employee has a Jibble ID (was synced from Jibble)
        if (!$employee->jibble_person_id) {
            return redirect()->back()
                ->with('error', 'This employee was not synced from Jibble and cannot be deleted this way.');
        }

        $name = $employee->name;

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

        return redirect()->route('admin.users.index')
            ->with('success', "Successfully deleted Jibble employee: {$name}");
    }
}
