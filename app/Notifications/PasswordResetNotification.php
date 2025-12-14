<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public $newPassword;

    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Reset - Action Required')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your password has been reset by a system administrator.')
            ->line('**Your new temporary password is:**')
            ->line('```')
            ->line($this->newPassword)
            ->line('```')
            ->line('**Important:** Please follow these steps:')
            ->line('1. Log in to your account using the temporary password above')
            ->line('2. Navigate to your profile settings')
            ->line('3. Change your password immediately for security purposes')
            ->action('Login to Your Account', url('/login'))
            ->line('If you did not request a password reset or have concerns about this change, please contact your system administrator immediately.')
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
