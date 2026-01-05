<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Status Changed</title>
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
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            padding: 40px 20px;
            text-align: center;
        }

        .header-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 15px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 30px 20px;
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

        .status-change {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 24px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .status-flow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 15px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-old {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .status-new {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .arrow {
            color: #6b7280;
            font-size: 20px;
        }

        .lead-details {
            background: #f9fafb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .lead-details h3 {
            margin: 0 0 15px 0;
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #6b7280;
            width: 140px;
            flex-shrink: 0;
        }

        .detail-value {
            color: #1f2937;
            flex-grow: 1;
        }

        .cta-button {
            display: inline-block;
            background: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }

        .cta-button:hover {
            background: #2563eb;
            box-shadow: 0 6px 8px rgba(59, 130, 246, 0.3);
        }

        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }

        .footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M12 12H15M12 16H15M9 12H9.01M9 16H9.01" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1>Lead Status Updated</h1>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $lead->assignedTo->name ?? 'Team' }},
            </div>

            <div class="message">
                The status of the following lead has been updated by <strong>{{ $changedBy->name }}</strong>.
            </div>

            <div class="status-change">
                <h3 style="margin-top: 0;">Status Change</h3>
                <div class="status-flow">
                    <span class="status-badge status-old">{{ $oldStatus }}</span>
                    <span class="arrow">→</span>
                    <span class="status-badge status-new">{{ $newStatus }}</span>
                </div>
            </div>

            <div class="lead-details">
                <h3>Lead Information</h3>

                <div class="detail-row">
                    <div class="detail-label">Client Name:</div>
                    <div class="detail-value">{{ $lead->client_name }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Service Type:</div>
                    <div class="detail-value">{{ $lead->service_requested }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value">{{ $lead->location }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Contact Email:</div>
                    <div class="detail-value">
                        <a href="mailto:{{ $lead->email }}" style="color: #3b82f6; font-weight: 500;">{{ $lead->email }}</a>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Contact Phone:</div>
                    <div class="detail-value">
                        <a href="tel:{{ $lead->phone_number }}" style="color: #3b82f6; font-weight: 500;">{{ $lead->phone_number }}</a>
                    </div>
                </div>

                @if($lead->inspection_date)
                <div class="detail-row">
                    <div class="detail-label">Inspection Date:</div>
                    <div class="detail-value">
                        {{ $lead->inspection_date->format('F j, Y') }}
                        @if($lead->inspection_time)
                        at {{ $lead->inspection_time->format('h:i A') }}
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/admin/leads/' . $lead->id) }}" class="cta-button">
                    View Lead Details
                </a>
            </div>

            @if($newStatus === 'Inspection Booked' && $lead->inspection_date)
            <div class="message"
                style="margin-top: 20px; padding: 16px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                <strong style="color: #d97706;">⏰ Reminder:</strong> An inspection is scheduled for {{ $lead->inspection_date->format('F j, Y')
                }}
                @if($lead->inspection_time)
                at {{ $lead->inspection_time->format('h:i A') }}
                @endif
            </div>
            @endif

            @if($newStatus === 'Positive')
            <div class="message"
                style="margin-top: 20px; padding: 16px; background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);">
                <strong style="color: #059669;">🎉 Great news!</strong> This lead has converted to a positive client. Please ensure all
                follow-up actions are completed.
            </div>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated notification from the ERP System.</p>
            <p>
                Questions? <a href="{{ url('/admin/leads') }}">Visit Leads Dashboard</a>
            </p>
        </div>
    </div>
</body>

</html>