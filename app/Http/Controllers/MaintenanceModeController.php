<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MaintenanceModeController extends Controller
{
    /**
     * Get maintenance mode status
     */
    public function status()
    {
        $isEnabled = Cache::get('system_maintenance_mode', false);
        $message = Cache::get('system_maintenance_message', config('maintenance.default_message'));
        $canManage = false;

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $canManage = $user->hasRole(['super_admin', 'admin']);
        }

        return response()->json([
            'enabled' => $isEnabled,
            'message' => $message,
            'can_manage' => $canManage,
        ]);
    }

    /**
     * Enable maintenance mode
     */
    public function enable(Request $request)
    {
        // Only admins can enable maintenance mode
        /** @var User $user */
        $user = Auth::user();
        if (!$user->hasRole(['super_admin', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        $message = $request->input('message', config('maintenance.default_message'));

        Cache::forever('system_maintenance_mode', true);
        Cache::forever('system_maintenance_message', $message);

        return response()->json([
            'message' => 'Maintenance mode enabled successfully.',
            'enabled' => true,
            'maintenance_message' => $message,
        ]);
    }

    /**
     * Disable maintenance mode
     */
    public function disable()
    {
        // Only admins can disable maintenance mode
        /** @var User $user */
        $user = Auth::user();
        if (!$user->hasRole(['super_admin', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        Cache::forget('system_maintenance_mode');
        Cache::forget('system_maintenance_message');

        return response()->json([
            'message' => 'Maintenance mode disabled successfully.',
            'enabled' => false,
        ]);
    }

    /**
     * Update maintenance message
     */
    public function updateMessage(Request $request)
    {
        // Only admins can update maintenance message
        /** @var User $user */
        $user = Auth::user();
        if (!$user->hasRole(['super_admin', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        Cache::forever('system_maintenance_message', $request->input('message'));

        return response()->json([
            'message' => 'Maintenance message updated successfully.',
            'maintenance_message' => $request->input('message'),
        ]);
    }
}
