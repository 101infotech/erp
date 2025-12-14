<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Received</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .message {
            margin-bottom: 25px;
            color: #4b5563;
        }

        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #4b5563;
        }

        .info-value {
            color: #1f2937;
        }

        .priority-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .priority-low {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .priority-medium {
            background-color: #fef3c7;
            color: #92400e;
        }

        .priority-high {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📬 Feedback Received</h1>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $user->name }},</div>

            <div class="message">
                Thank you for submitting your feedback. We have received it and our team will review it shortly.
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Subject:</span>
                    <span class="info-value">{{ $complaint->subject }}</span>
                </div>
                @if($complaint->category)
                <div class="info-row">
                    <span class="info-label">Category:</span>
                    <span class="info-value">{{ $complaint->category }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Priority:</span>
                    <span class="info-value">
                        <span class="priority-badge priority-{{ $complaint->priority }}">{{
                            ucfirst($complaint->priority) }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Submitted At:</span>
                    <span class="info-value">{{ $complaint->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Reference ID:</span>
                    <span class="info-value">#{{ $complaint->id }}</span>
                </div>
            </div>

            <div class="message">
                <strong>What happens next?</strong>
                <ul style="color: #6b7280; margin: 10px 0;">
                    <li>Our team will review your feedback within 24-48 hours</li>
                    <li>You'll receive updates as the status changes</li>
                    <li>You can track the status in your employee portal</li>
                    <li>We appreciate your input and will address it promptly</li>
                </ul>
            </div>

            <div class="message"
                style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; border-radius: 4px;">
                <strong style="color: #065f46;">Your privacy matters:</strong>
                <p style="margin: 10px 0 0 0; color: #047857;">
                    Your feedback is confidential and will be handled with care. We're committed to creating a better
                    workplace for everyone.
                </p>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
            <p>If you have any urgent concerns, please contact HR directly.</p>
        </div>
    </div>
</body>

</html>