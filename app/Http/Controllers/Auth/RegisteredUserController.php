<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\HrmEmployee;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // First, validate email format and check if it exists in HRM
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'exists:hrm_employees,email'],
        ], [
            'email.exists' => 'Registration is restricted. Your email must exist in the company HRM records.',
        ]);

        // Check if user already exists with this email
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return redirect()->route('password.request')
                ->with('status', 'An account already exists with this email. Please reset your password if you forgot it.');
        }

        // Validate remaining fields
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Prefer HRM name if available
        $employee = HrmEmployee::where('email', $request->email)->first();
        $name = $employee?->full_name ?: $request->name;

        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Link the employee to this user (one-time)
        if ($employee && !$employee->user_id) {
            $employee->user_id = $user->id;
            $employee->save();
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
