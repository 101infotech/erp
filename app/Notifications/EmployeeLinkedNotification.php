<?php

namespace App\Notifications;

use App\Models\HrmEmployee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeLinkedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public HrmEmployee $employee)
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
            ->subject('Your Account Has Been Linked to Employee Record')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your user account has been successfully linked to your employee record.')
            ->line('**Employee Details:**')
            ->line('Name: ' . $this->employee->name)
            ->line('Company: ' . ($this->employee->company->name ?? 'N/A'))
            ->line('Department: ' . ($this->employee->department->name ?? 'N/A'))
            ->line('This means you now have full access to the ERP system with your employee profile.')
            ->action('Access Dashboard', url('/dashboard'))
            ->line('If you have any questions, please contact your HR department.')
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
            'employee_id' => $this->employee->id,
            'employee_name' => $this->employee->name,
        ];
    }
}
