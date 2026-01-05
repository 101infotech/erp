<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $userName,
        public string $userEmail,
        public bool $hasEmployeeRecord = false
    ) {
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
        $message = (new MailMessage)
            ->subject('Account Deletion Notification')
            ->greeting('Hello ' . $this->userName . ',')
            ->line('This is to inform you that your user account (' . $this->userEmail . ') has been deleted from the system.')
            ->line('**Account Details:**')
            ->line('Name: ' . $this->userName)
            ->line('Email: ' . $this->userEmail);

        if ($this->hasEmployeeRecord) {
            $message->line('**Important:** Your employee record has been preserved and unlinked. This means your employment data remains in the system, but you no longer have login access.');
        }

        $message->line('**What this means:**')
            ->line('• You can no longer log into the system')
            ->line('• All access to company resources has been revoked')
            ->line('• Any data associated with your account may be retained per company policy')
            ->line('If you believe this was done in error or have questions, please contact your HR department immediately.')
            ->line('Thank you for your time with us.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
            'has_employee_record' => $this->hasEmployeeRecord,
        ];
    }
}
