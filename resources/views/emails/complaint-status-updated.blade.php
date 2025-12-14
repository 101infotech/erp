<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Status Updated</title>
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
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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
            background-color: #f3f4f6;
            border-left: 4px solid #8b5cf6;
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

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-reviewing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-resolved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-dismissed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .notes-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }

        .button:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Feedback Status Updated</h1>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $user->name }},</div>

            <div class="message">
                There's an update on your feedback. The status has been changed to <strong>{{
                    ucfirst($complaint->status) }}</strong>.
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
                    <span class="info-label">Current Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $complaint->status }}">{{ ucfirst($complaint->status)
                            }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Reference ID:</span>
                    <span class="info-value">#{{ $complaint->id }}</span>
                </div>
                @if($complaint->resolved_at)
                <div class="info-row">
                    <span class="info-label">Resolved At:</span>
                    <span class="info-value">{{ $complaint->resolved_at->format('d M Y, h:i A') }}</span>
                </div>
                @endif
            </div>

            @if($complaint->admin_notes)
            <div class="notes-box">
                <strong style="color: #92400e;">Admin Notes:</strong>
                <p style="margin: 10px 0 0 0; color: #78350f;">{{ $complaint->admin_notes }}</p>
            </div>
            @endif

            @if($complaint->status === 'resolved')
            <div class="message"
                style="background-color: #d1fae5; border-left: 4px solid #10b981; padding: 15px; border-radius: 4px;">
                <strong style="color: #065f46;">✅ Feedback Resolved</strong>
                <p style="margin: 10px 0 0 0; color: #047857;">
                    Thank you for bringing this to our attention. We've addressed your feedback and taken appropriate
                    action.
                    Your input helps us improve the workplace for everyone.
                </p>
            </div>
            @elseif($complaint->status === 'reviewing')
            <div class="message"
                style="background-color: #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 4px;">
                <strong style="color: #1e40af;">👀 Under Review</strong>
                <p style="margin: 10px 0 0 0; color: #1e3a8a;">
                    Your feedback is currently being reviewed by our team. We'll update you once we have more
                    information.
                </p>
            </div>
            @elseif($complaint->status === 'dismissed')
            <div class="message"
                style="background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 4px;">
                <strong style="color: #991b1b;">Feedback Dismissed</strong>
                <p style="margin: 10px 0 0 0; color: #7f1d1d;">
                    After review, this feedback has been dismissed. If you have concerns about this decision, please
                    contact HR directly.
                </p>
            </div>
            @endif

            <div style="text-align: center;">
                <a href="{{ route('employee.complaints.show', $complaint->id) }}" class="button">View Feedback
                    Details</a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
            <p>If you have any questions, please contact your HR department.</p>
        </div>
    </div>
</body>

</html>