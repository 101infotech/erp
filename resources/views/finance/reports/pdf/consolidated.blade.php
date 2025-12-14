<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Consolidated Financial Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4472C4;
        }

        .company-logo {
            font-size: 24pt;
            font-weight: bold;
            color: #4472C4;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px;
        }

        .report-period {
            font-size: 10pt;
            color: #7f8c8d;
            font-style: italic;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.main-table th {
            background-color: #4472C4;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        }

        table.main-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ecf0f1;
        }

        table.main-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .grand-total-row {
            font-weight: bold;
            font-size: 11pt;
            background-color: #d4e6f1 !important;
            border-top: 3px double #4472C4;
            border-bottom: 3px double #4472C4;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
            padding-top: 10px;
            border-top: 1px solid #ecf0f1;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-logo">{{ config('app.name', 'ERP System') }}</div>
        <div class="report-title">{{ $title }}</div>
        <div class="report-period">Fiscal Year: {{ $report['fiscal_year'] }}</div>
    </div>

    <table class="main-table">
        <tr>
            <th>Company</th>
            <th class="text-right">Revenue</th>
            <th class="text-right">Expenses</th>
            <th class="text-right">Net Profit</th>
            <th class="text-right">Profit Margin</th>
        </tr>
        @foreach($report['companies'] as $company)
        <tr>
            <td>{{ $company['company'] }}</td>
            <td class="text-right">{{ number_format($company['revenue'], 2) }}</td>
            <td class="text-right">{{ number_format($company['expenses'], 2) }}</td>
            <td class="text-right">{{ number_format($company['net_profit'], 2) }}</td>
            <td class="text-right">{{ number_format($company['profit_margin'], 2) }}%</td>
        </tr>
        @endforeach
        <tr class="grand-total-row">
            <td><strong>GROUP TOTALS</strong></td>
            <td class="text-right"><strong>{{ number_format($report['group_totals']['total_revenue'], 2) }}</strong>
            </td>
            <td class="text-right"><strong>{{ number_format($report['group_totals']['total_expenses'], 2) }}</strong>
            </td>
            <td class="text-right"><strong>{{ number_format($report['group_totals']['total_net_profit'], 2) }}</strong>
            </td>
            <td class="text-right"><strong>{{ number_format($report['group_totals']['average_profit_margin'], 2)
                    }}%</strong></td>
        </tr>
    </table>

    <div class="footer">
        <div>Generated on: {{ $generated_at }}</div>
    </div>
</body>

</html>