<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Rejected</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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

        .warning-box {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
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
            background-color: #fee2e2;
            color: #991b1b;
        }

        .reason-box {
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
            <h1>❌ Leave Request Not Approved</h1>
        </div>

        <div class="content">
            <div class="greeting">Hello {{ $employee->name }},</div>

            <div class="message">
                We regret to inform you that your leave request has been <strong>rejected</strong> by
                {{ $approver->name }}.
            </div>

            <div class="warning-box">
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
                    <span class="info-label">Reviewed By:</span>
                    <span class="info-value">{{ $approver->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge">Rejected</span>
                    </span>
                </div>
            </div>

            @if($leave->rejection_reason)
            <div class="reason-box">
                <strong style="color: #92400e;">Reason for Rejection:</strong>
                <p style="margin: 10px 0 0 0; color: #78350f;">{{ $leave->rejection_reason }}</p>
            </div>
            @endif

            <div class="message">
                <strong>What can you do next?</strong>
                <ul style="color: #6b7280; margin: 10px 0;">
                    <li>Discuss with your manager about the rejection reason</li>
                    <li>Consider submitting a new request for different dates</li>
                    <li>Contact HR if you have concerns about the decision</li>
                    <li>Your leave balance remains unchanged</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('employee.leave.create') }}" class="button">Submit New Request</a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
            <p>If you have any questions, please contact your HR department.</p>
        </div>
    </div>
</body>

</html>