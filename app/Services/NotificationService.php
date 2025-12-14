<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?array $data = null
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'data' => $data,
        ]);
    }

    /**
     * Create notifications for all admins
     */
    public function notifyAdmins(
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?array $data = null
    ): void {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $this->createNotification($admin->id, $type, $title, $message, $link, $data);
        }
    }

    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecentNotifications(int $userId, int $limit = 10)
    {
        return Notification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all notifications for a user (paginated)
     */
    public function getAllNotifications(int $userId)
    {
        return Notification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): void
    {
        Notification::forUser($userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);

        if ($notification) {
            return $notification->delete();
        }

        return false;
    }
}
