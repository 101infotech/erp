<?php

namespace App\Mail;

use App\Models\HrmPayrollRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PayrollSentMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public HrmPayrollRecord $payrollRecord,
        public string $pdfPath
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $employee = $this->payrollRecord->employee;
        $period = $this->payrollRecord->period_start_bs . ' - ' . $this->payrollRecord->period_end_bs;

        return new Envelope(
            subject: "Your Payslip for {$period}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payroll-sent',
            with: [
                'employeeName' => $this->payrollRecord->employee->name,
                'period' => $this->payrollRecord->period_start_bs . ' - ' . $this->payrollRecord->period_end_bs,
                'netSalary' => number_format($this->payrollRecord->net_salary, 2),
                'grossSalary' => number_format($this->payrollRecord->gross_salary, 2),
                'deductions' => number_format($this->payrollRecord->deductions_total + $this->payrollRecord->tax_amount, 2),
                'sentBy' => $this->payrollRecord->sent_by_name ?? 'HR Department',
                'sentAt' => $this->payrollRecord->sent_at?->format('d M Y, h:i A'),
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
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Payslip_' . str_replace(['/', ' '], '_', $this->payrollRecord->period_start_bs) . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
