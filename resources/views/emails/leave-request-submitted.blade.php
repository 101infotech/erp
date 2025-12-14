<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Submitted</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 4px solid #a78bfa;
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
            background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
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
            <h1>📝 Leave Request Submitted</h1>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $employee->name }},</div>

            <div class="message">
                Your leave request has been successfully submitted and is now <strong>pending approval</strong> from
                your manager.
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Leave Type:</span>
                    <span class="info-value">{{ ucfirst($leave->leave_type) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">From:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">To:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Days:</span>
                    <span class="info-value">{{ $leave->total_days }} day(s)</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Reason:</span>
                    <span class="info-value">{{ $leave->reason }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-pending">Pending</span>
                    </span>
                </div>
            </div>

            <div class="message">
                <strong>What happens next?</strong>
                <ul style="color: #6b7280; margin: 10px 0;">
                    <li>Your manager will review your request</li>
                    <li>You'll receive an email once it's approved or rejected</li>
                    <li>You can track the status in your employee portal</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('employee.leave.show', $leave->id) }}" class="button">View Leave Request</a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
            <p>If you have any questions, please contact your HR department.</p>
        </div>
    </div>
</body>

</html>