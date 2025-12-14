<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $employee->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #222;
        }

        .header .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #555;
            margin-bottom: 3px;
        }

        .header .document-title {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .info-cell.label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 30%;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #333;
            color: #222;
        }

        .attendance-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .attendance-row {
            display: table-row;
        }

        .attendance-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            width: 14.28%;
        }

        .attendance-cell.header {
            background-color: #333;
            color: white;
            font-weight: bold;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .salary-table th,
        .salary-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .salary-table th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }

        .salary-table td.amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        .salary-table tr.subtotal td {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .salary-table tr.total td {
            font-weight: bold;
            background-color: #333;
            color: white;
            font-size: 13px;
        }

        .anomalies-list {
            margin-top: 10px;
        }

        .anomaly-item {
            padding: 8px;
            margin-bottom: 5px;
            border-left: 3px solid #fbbf24;
            background-color: #fef3c7;
        }

        .anomaly-item.critical {
            border-left-color: #ef4444;
            background-color: #fee2e2;
        }

        .anomaly-item.major {
            border-left-color: #f97316;
            background-color: #ffedd5;
        }

        .anomaly-item strong {
            display: block;
            margin-bottom: 3px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @if($payrollRecord->status === 'draft')
    <div class="watermark">DRAFT</div>
    @endif

    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
            @if($company && $company->address)
            <div style="font-size: 10px; color: #777;">{{ $company->address }}</div>
            @endif
            <div class="document-title">PAYSLIP</div>
            <div style="font-size: 10px; margin-top: 5px;">
                Period: {{ $payrollRecord->period_start_bs }} to {{ $payrollRecord->period_end_bs }}
            </div>
        </div>

        {{-- Employee Information --}}
        <div class="section-title">Employee Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell label">Employee Name</div>
                <div class="info-cell">{{ $employee->name }}</div>
                <div class="info-cell label">Employee Code</div>
                <div class="info-cell">{{ $employee->employee_code ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell label">Department</div>
                <div class="info-cell">{{ $department->name ?? 'N/A' }}</div>
                <div class="info-cell label">Designation</div>
                <div class="info-cell">{{ $employee->designation ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell label">PAN Number</div>
                <div class="info-cell">{{ $employee->pan_number ?? 'N/A' }}</div>
                <div class="info-cell label">Bank Account</div>
                <div class="info-cell">{{ $employee->bank_account_number ?? 'N/A' }}</div>
            </div>
        </div>

        {{-- Attendance Summary --}}
        <div class="section-title">Attendance Summary</div>
        <div class="attendance-grid">
            <div class="attendance-row">
                @foreach($attendanceSummary as $label => $value)
                <div class="attendance-cell header">{{ $label }}</div>
                @endforeach
            </div>
            <div class="attendance-row">
                @foreach($attendanceSummary as $label => $value)
                <div class="attendance-cell">{{ $value }}</div>
                @endforeach
            </div>
        </div>

        {{-- Salary Breakdown --}}
        <div class="section-title">Salary Breakdown</div>
        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Description</th>
                    <th style="width: 30%;">Amount (NPR)</th>
                </tr>
            </thead>
            <tbody>
                {{-- Earnings --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="2"><strong>Earnings</strong></td>
                </tr>
                @foreach($earnings as $earning)
                <tr>
                    <td>{{ $earning['label'] }}</td>
                    <td class="amount">{{ number_format($earning['amount'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="subtotal">
                    <td>Total Earnings</td>
                    <td class="amount">{{ number_format($totalEarnings, 2) }}</td>
                </tr>

                {{-- Deductions --}}
                <tr style="background-color: #f9fafb;">
                    <td colspan="2"><strong>Deductions</strong></td>
                </tr>
                @foreach($deductions as $deduction)
                <tr>
                    <td>{{ $deduction['label'] }}</td>
                    <td class="amount">{{ number_format($deduction['amount'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="subtotal">
                    <td>Total Deductions</td>
                    <td class="amount">{{ number_format($totalDeductions, 2) }}</td>
                </tr>

                {{-- Net Salary --}}
                <tr class="total">
                    <td>Net Salary Payable</td>
                    <td class="amount">{{ number_format($netSalary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Anomalies (if any) --}}
        @if(count($anomalies) > 0)
        <div class="section-title">Attendance Anomalies</div>
        <div class="anomalies-list">
            @foreach($anomalies as $anomaly)
            <div class="anomaly-item {{ strtolower($anomaly['severity']) }}">
                <strong>{{ ucfirst($anomaly['type']) }} - {{ $anomaly['date'] }}</strong>
                <div>{{ $anomaly['description'] }}</div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Approval Information --}}
        @if($payrollRecord->status === 'approved' || $payrollRecord->status === 'paid')
        <div class="section-title">Approval Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell label">Approved By</div>
                <div class="info-cell">{{ $payrollRecord->approved_by_name ?? 'N/A' }}</div>
                <div class="info-cell label">Approved On</div>
                <div class="info-cell">{{ $payrollRecord->approved_at?->format('d M Y, h:i A') ?? 'N/A' }}</div>
            </div>
            @if($payrollRecord->status === 'paid')
            <div class="info-row">
                <div class="info-cell label">Payment Method</div>
                <div class="info-cell">{{ ucfirst($payrollRecord->payment_method ?? 'N/A') }}</div>
                <div class="info-cell label">Paid On</div>
                <div class="info-cell">{{ $payrollRecord->paid_at?->format('d M Y, h:i A') ?? 'N/A' }}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Employee Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>This is a computer-generated payslip and does not require a physical signature.</p>
            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
            @if($payrollRecord->status === 'draft')
            <p style="color: #ef4444; font-weight: bold; margin-top: 5px;">DRAFT - NOT FOR OFFICIAL USE</p>
            @endif
        </div>
    </div>
</body>

</html>