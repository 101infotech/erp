<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $employee = Auth::user()->hrmEmployee;

        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee profile not found.');
        }

        return view('employee.profile.show', compact('employee'));
    }

    public function edit()
    {
        $employee = Auth::user()->hrmEmployee;

        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee profile not found.');
        }

        return view('employee.profile.edit', compact('employee'));
    }

    public function update(Request $request)
    {
        $employee = Auth::user()->hrmEmployee;

        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
        ]);

        $employee->update($validated);

        return redirect()->route('employee.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function uploadAvatar(Request $request)
    {
        $employee = Auth::user()->hrmEmployee;

        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found.'], 404);
        }

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // 2MB max
        ]);

        // Delete old avatar if exists
        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('hrm/avatars', 'public');
        $avatarUrl = Storage::url($path);

        $employee->update([
            'avatar' => $path,
            'avatar_url' => $avatarUrl
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile image updated successfully.',
            'avatar_url' => asset('storage/' . $path)
        ]);
    }

    public function deleteAvatar()
    {
        $employee = Auth::user()->hrmEmployee;

        if (!$employee) {
            return response()->json(['error' => 'Employee profile not found.'], 404);
        }

        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
            $employee->update([
                'avatar' => null,
                'avatar_url' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile image removed successfully.'
        ]);
    }
}
