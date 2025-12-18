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
        $query = User::with('hrmEmployee.company', 'hrmEmployee.department')->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Company filter (via HrmEmployee)
        if ($request->filled('company_id')) {
            $companyId = $request->company_id;
            $query->whereHas('hrmEmployee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $users = $query->paginate(15);
        $companies = \App\Models\HrmCompany::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'companies'));
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

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
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
}
