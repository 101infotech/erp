<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip Approved</title>
</head>

<body
    style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div
        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #84cc16; margin: 0; font-size: 28px;">Payslip Approved</h1>
    </div>

    <div
        style="background-color: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">Dear <strong>{{ $employeeName }}</strong>,</p>

        <p style="font-size: 14px; margin-bottom: 15px;">
            Your payslip for the period <strong>{{ $period }}</strong> has been approved and is ready for payment.
        </p>

        <div
            style="background-color: #f9fafb; border-left: 4px solid #84cc16; padding: 20px; margin: 25px 0; border-radius: 5px;">
            <h2 style="margin: 0 0 15px 0; color: #1f2937; font-size: 18px;">Payslip Summary</h2>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Period:</td>
                    <td style="padding: 8px 0; font-weight: bold; text-align: right;">{{ $period }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Net Salary:</td>
                    <td style="padding: 8px 0; font-weight: bold; text-align: right; color: #84cc16; font-size: 18px;">
                        NPR {{ $netSalary }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Approved By:</td>
                    <td style="padding: 8px 0; font-weight: bold; text-align: right;">{{ $approvedBy }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Approved On:</td>
                    <td style="padding: 8px 0; font-weight: bold; text-align: right;">{{ $approvedAt }}</td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; margin-bottom: 15px;">
            Your detailed payslip is attached to this email as a PDF document. Please review it and keep it for your
            records.
        </p>

        <div
            style="background-color: #fef3c7; border: 1px solid #fbbf24; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p style="margin: 0; font-size: 13px; color: #92400e;">
                <strong>📌 Important:</strong> If you notice any discrepancies in your payslip, please contact the HR
                department immediately.
            </p>
        </div>

        <p style="font-size: 14px; margin-top: 25px;">
            If you have any questions or concerns, please don't hesitate to reach out to the HR department.
        </p>

        <p style="font-size: 14px; margin-top: 20px;">
            Best regards,<br>
            <strong>HR Department</strong>
        </p>
    </div>

    <div style="text-align: center; padding: 20px; font-size: 12px; color: #6b7280;">
        <p style="margin: 5px 0;">This is an automated email. Please do not reply to this message.</p>
        <p style="margin: 5px 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>