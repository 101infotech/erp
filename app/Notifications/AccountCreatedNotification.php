<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password = $password;
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
            ->subject('Your ERP Account Has Been Created')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your account has been created in the Saubhagya ERP system.')
            ->line('**Your Login Credentials:**')
            ->line('Email: ' . $notifiable->email)
            ->line('Password: ' . $this->password)
            ->line('')
            ->line('**Important Security Notice:**')
            ->line('• Please change your password after your first login')
            ->line('• Do not share your credentials with anyone')
            ->line('• Keep your password secure and confidential')
            ->action('Login to ERP', url('/login'))
            ->line('If you have any questions or need assistance, please contact your administrator.')
            ->salutation('Best regards, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your ERP account has been created',
            'email' => $notifiable->email,
        ];
    }
}
