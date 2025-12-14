<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Redirect admin users to admin dashboard
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Please use the admin dashboard to access HRM features.');
        }

        return $next($request);
    }
}
