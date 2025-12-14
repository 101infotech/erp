<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Complaint $complaint
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusEmoji = match ($this->complaint->status) {
            'resolved' => '✅',
            'dismissed' => '❌',
            'reviewing' => '👀',
            default => '🔔',
        };

        return new Envelope(
            subject: $statusEmoji . ' Feedback Status Updated - ' . ucfirst($this->complaint->status),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complaint-status-updated',
            with: [
                'complaint' => $this->complaint,
                'user' => $this->complaint->user,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
