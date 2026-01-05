<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Assigned</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
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

        .lead-details {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            background: #3b82f6;
            color: #ffffff;
        }

        .cta-button {
            display: inline-block;
            background: #10b981;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }

        .cta-button:hover {
            background: #059669;
            box-shadow: 0 6px 8px rgba(16, 185, 129, 0.3);
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
            color: #10b981;
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
                    <path
                        d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <h1>New Service Lead Assigned</h1>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $assignedUser->name }},
            </div>

            <div class="message">
                A new service lead has been assigned to you. Please review the details below and take appropriate
                action.
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
                        <a href="mailto:{{ $lead->email }}" style="color: #10b981; font-weight: 500;">{{ $lead->email
                            }}</a>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Contact Phone:</div>
                    <div class="detail-value">
                        <a href="tel:{{ $lead->phone_number }}" style="color: #10b981; font-weight: 500;">{{
                            $lead->phone_number }}</a>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge">{{ $lead->status }}</span>
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

                @if($lead->inspection_charge)
                <div class="detail-row">
                    <div class="detail-label">Inspection Charge:</div>
                    <div class="detail-value">${{ number_format($lead->inspection_charge, 2) }}</div>
                </div>
                @endif

                @if($lead->message)
                <div class="detail-row">
                    <div class="detail-label">Client Message:</div>
                    <div class="detail-value">{{ $lead->message }}</div>
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/admin/leads/' . $lead->id) }}" class="cta-button">
                    View Lead Details
                </a>
            </div>

            <div class="message" style="margin-top: 20px; font-size: 14px;">
                Please contact the client at your earliest convenience to discuss their requirements and schedule the
                service.
            </div>
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