<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceEnabled = Cache::get('system_maintenance_mode', false);

        if (!$maintenanceEnabled) {
            return $next($request);
        }

        // Allow access for specific routes (login, logout, maintenance page)
        $allowedRoutes = [
            'login',
            'logout',
            'maintenance.status',
            'sanctum/*',
        ];

        foreach ($allowedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->maintenanceResponse($request);
        }

        /** @var User $user */
        $user = Auth::user();

        // Allow admins and super admins to bypass maintenance mode
        if ($user->hasRole(['super_admin', 'admin'])) {
            // Add header to indicate maintenance mode is active
            return $next($request)->header('X-Maintenance-Mode', 'bypassed-admin');
        }

        // Allow specific user IDs to bypass (configured in config/maintenance.php)
        $allowedUserIds = config('maintenance.allowed_user_ids', []);
        if (in_array($user->id, $allowedUserIds)) {
            return $next($request)->header('X-Maintenance-Mode', 'bypassed-whitelist');
        }

        // All other users see maintenance page
        return $this->maintenanceResponse($request);
    }

    /**
     * Return maintenance response
     */
    private function maintenanceResponse(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'System is currently under maintenance. Please try again later.',
                'maintenance_mode' => true,
            ], 503);
        }

        return response()->view('errors.maintenance', [
            'message' => Cache::get('system_maintenance_message', 'We are currently performing scheduled maintenance. We\'ll be back shortly!'),
        ], 503);
    }
}
