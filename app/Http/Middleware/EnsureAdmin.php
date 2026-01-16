<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized. Authentication required.');
        }

        // Check if user has admin or super_admin role using the roles system
        if (!$user->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized. Admins only.');
        }

        return $next($request);
    }
}
