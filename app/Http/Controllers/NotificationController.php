<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show all notifications page
     */
    public function index()
    {
        $notifications = $this->notificationService->getAllNotifications(Auth::id());
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());

        // Determine which layout to use based on user role
        if (Auth::user()->role === 'admin') {
            return view('admin.notifications.index', compact('notifications', 'unreadCount'));
        }

        return view('employee.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get notifications for current user (API endpoint)
     */
    public function all()
    {
        $notifications = $this->notificationService->getRecentNotifications(Auth::id(), 50);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get unread count
     */
    public function unreadCount()
    {
        $count = $this->notificationService->getUnreadCount(Auth::id());
        return response()->json(['count' => $count]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $success = $this->notificationService->markAsRead($id);

        if ($success) {
            // Check if request expects JSON (AJAX request)
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true]);
            }

            // Redirect back to notifications page for regular requests
            return redirect()->back()->with('success', 'Notification marked as read');
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => false], 404);
        }

        return redirect()->back()->with('error', 'Notification not found');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());
        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $success = $this->notificationService->deleteNotification($id);

        if ($success) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
