<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Changed - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your account password has been changed by a system administrator.')
            ->line('If you did not request this change or have any concerns, please contact your system administrator immediately.')
            ->line('For security purposes, we recommend:')
            ->line('• Reviewing your recent account activity')
            ->line('• Ensuring you can log in with your new password')
            ->line('• Changing your password again if you suspect unauthorized access')
            ->action('Login to Your Account', url('/login'))
            ->line('If you have any questions or concerns, please contact your administrator.')
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
