<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCanManageLeads
{
    /**
     * Handle an incoming request.
     * 
     * Access control for service leads management:
     * - Admins: Full access to all actions (always allowed)
     * - Employees: Can view and update status of assigned leads only (if can_access_leads is true)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = 'view'): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized. Authentication required.');
        }

        // Admins have full access to all actions
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Employee access is limited based on action and permission
        if ($user->role === 'employee') {
            // Check if employee has access to leads module
            if (!$user->can_access_leads) {
                abort(403, 'You do not have access to the leads management module. Please contact your administrator.');
            }

            // Employees can only view and edit (status updates) on their assigned leads
            if (in_array($action, ['view', 'edit'])) {
                // If viewing list or viewing/editing a specific lead
                $lead = $request->route('lead');

                if (!$lead) {
                    // Viewing list - controller will filter to assigned leads only
                    return $next($request);
                }

                // Check if employee is assigned to this lead
                if ($this->isViewingOwnLead($request, $user)) {
                    return $next($request);
                }

                abort(403, 'You can only access leads assigned to you.');
            }

            // Employees cannot create, delete, assign, or view analytics
            abort(403, 'You do not have permission to perform this action.');
        }

        // Any other role - deny access
        abort(403, 'You do not have permission to access this module.');
    }

    /**
     * Check if the employee is viewing their own assigned lead
     */
    private function isViewingOwnLead(Request $request, $user): bool
    {
        $lead = $request->route('lead');

        if (!$lead) {
            return false;
        }

        // Allow if lead is assigned to this user
        return $lead->inspection_assigned_to === $user->id;
    }
}
