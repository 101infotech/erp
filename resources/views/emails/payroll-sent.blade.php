<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip Sent</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #84cc16;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #84cc16;
            margin-bottom: 10px;
        }

        h1 {
            color: #1e293b;
            font-size: 24px;
            margin: 0 0 10px 0;
        }

        .period {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        .greeting {
            font-size: 16px;
            color: #334155;
            margin-bottom: 20px;
        }

        .details {
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .details-row:last-child {
            border-bottom: none;
        }

        .label {
            color: #64748b;
            font-size: 14px;
        }

        .value {
            color: #1e293b;
            font-weight: 600;
            font-size: 14px;
        }

        .value.highlight {
            color: #84cc16;
            font-size: 18px;
        }

        .message {
            color: #475569;
            font-size: 14px;
            line-height: 1.8;
            margin: 20px 0;
        }

        .attachment-notice {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .attachment-notice p {
            margin: 0;
            color: #1e40af;
            font-size: 14px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #84cc16;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 0;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
            }

            .details-row {
                flex-direction: column;
            }

            .value {
                margin-top: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Saubhagya ERP</div>
            <h1>Payslip for {{ $period }}</h1>
        </div>

        <p class="greeting">Dear {{ $employeeName }},</p>

        <p class="message">
            Your payslip for the period <strong>{{ $period }}</strong> has been processed and is now available.
            Please find your detailed payslip attached to this email.
        </p>

        <div class="details">
            <div class="details-row">
                <span class="label">Gross Salary:</span>
                <span class="value">NPR {{ $grossSalary }}</span>
            </div>
            <div class="details-row">
                <span class="label">Deductions (Tax + Others):</span>
                <span class="value">NPR {{ $deductions }}</span>
            </div>
            <div class="details-row">
                <span class="label">Net Salary:</span>
                <span class="value highlight">NPR {{ $netSalary }}</span>
            </div>
        </div>

        <div class="attachment-notice">
            <p>
                <strong>📎 Attachment:</strong> Your complete payslip is attached as a PDF document.
                Please download and keep it for your records.
            </p>
        </div>

        <p class="message">
            If you have any questions or concerns regarding your payslip, please don't hesitate to contact the HR
            department.
        </p>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>Sent by {{ $sentBy }} on {{ $sentAt }}</p>
            <p style="margin-top: 10px;">&copy; {{ date('Y') }} Saubhagya ERP. All rights reserved.</p>
        </div>
    </div>
</body>

</html>