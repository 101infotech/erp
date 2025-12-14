<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Financial Report' }}</title>
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

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .company-details {
            font-size: 9pt;
            color: #7f8c8d;
            margin-bottom: 10px;
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

        .content {
            margin-top: 20px;
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

        .section-header {
            background-color: #34495e !important;
            color: white !important;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            font-size: 11pt;
        }

        .subsection-header {
            background-color: #7f8c8d !important;
            color: white !important;
            font-weight: bold;
            padding: 6px 8px;
            font-size: 10pt;
        }

        .total-row {
            font-weight: bold;
            background-color: #e8f4f8 !important;
            border-top: 2px solid #4472C4;
            border-bottom: 2px solid #4472C4;
        }

        .grand-total-row {
            font-weight: bold;
            font-size: 11pt;
            background-color: #d4e6f1 !important;
            border-top: 3px double #4472C4;
            border-bottom: 3px double #4472C4;
        }

        .indent-1 {
            padding-left: 20px !important;
        }

        .indent-2 {
            padding-left: 40px !important;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
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

        .page-number:before {
            content: "Page " counter(page);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9pt;
        }

        .status-balanced {
            background-color: #d4edda;
            color: #155724;
        }

        .status-unbalanced {
            background-color: #f8d7da;
            color: #721c24;
        }

        .positive-amount {
            color: #27ae60;
        }

        .negative-amount {
            color: #e74c3c;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-logo">{{ config('app.name', 'ERP System') }}</div>
        @if(isset($company))
        <div class="company-name">{{ $company->name }}</div>
        <div class="company-details">
            @if($company->address){{ $company->address }} | @endif
            @if($company->phone)Phone: {{ $company->phone }} | @endif
            @if($company->email)Email: {{ $company->email }}@endif
        </div>
        @endif
        <div class="report-title">{{ $title }}</div>
        @if(isset($report['period']))
        <div class="report-period">Period: {{ $report['period'] }}</div>
        @elseif(isset($report['as_of_date']))
        <div class="report-period">As of: {{ $report['as_of_date'] }}</div>
        @endif
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        <div>Generated on: {{ $generated_at }}</div>
        <div class="page-number"></div>
    </div>
</body>

</html>