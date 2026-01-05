<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeUnlinkedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $employeeName)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account Has Been Unlinked from Employee Record')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your user account has been unlinked from the employee record: **' . $this->employeeName . '**.')
            ->line('This change was made by an administrator.')
            ->line('**What this means:**')
            ->line('• Your login credentials remain the same')
            ->line('• Access to employee-specific features may be limited')
            ->line('• Contact HR if you believe this was done in error')
            ->line('If you have any questions or concerns, please contact your HR department.')
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'employee_name' => $this->employeeName,
        ];
    }
}
