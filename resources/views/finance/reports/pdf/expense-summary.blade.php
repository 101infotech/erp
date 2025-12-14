@extends('finance.reports.pdf.layout')

@section('content')
<table class="main-table">
    <tr class="section-header">
        <td colspan="3">MONTHLY BREAKDOWN</td>
    </tr>
    <tr>
        <th>Month</th>
        <th>Month Name</th>
        <th class="text-right">Total Expenses</th>
    </tr>
    @foreach($report['monthly_breakdown'] as $month)
    <tr>
        <td class="text-center">{{ $month['month'] }}</td>
        <td>{{ $month['month_name'] }}</td>
        <td class="text-right">{{ number_format($month['total_expenses'], 2) }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td colspan="2"><strong>TOTAL</strong></td>
        <td class="text-right"><strong>{{ number_format($report['total_expenses'], 2) }}</strong></td>
    </tr>
    <tr>
        <td colspan="2"><strong>AVERAGE</strong></td>
        <td class="text-right"><strong>{{ number_format($report['average_monthly_expense'], 2) }}</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="section-header">
        <td colspan="4">CATEGORY ANALYSIS</td>
    </tr>
    <tr>
        <th>Category</th>
        <th class="text-right">Total Amount</th>
        <th class="text-right">Percentage</th>
        <th class="text-right">Count</th>
    </tr>
    @foreach($report['category_analysis'] as $category)
    <tr>
        <td>{{ $category['category'] }}</td>
        <td class="text-right">{{ number_format($category['total'], 2) }}</td>
        <td class="text-right">{{ number_format($category['percentage'], 2) }}%</td>
        <td class="text-right">{{ $category['count'] }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td><strong>TOTAL</strong></td>
        <td class="text-right"><strong>{{ number_format($report['total_expenses'], 2) }}</strong></td>
        <td class="text-right"><strong>100%</strong></td>
        <td class="text-right">-</td>
    </tr>
</table>

@if(!empty($report['payment_methods']))
<table class="main-table">
    <tr class="section-header">
        <td colspan="3">PAYMENT METHODS</td>
    </tr>
    <tr>
        <th>Payment Method</th>
        <th class="text-right">Total Amount</th>
        <th class="text-right">Percentage</th>
    </tr>
    @foreach($report['payment_methods'] as $method)
    <tr>
        <td>{{ ucfirst(str_replace('_', ' ', $method['method'])) }}</td>
        <td class="text-right">{{ number_format($method['total'], 2) }}</td>
        <td class="text-right">{{ number_format($method['percentage'], 2) }}%</td>
    </tr>
    @endforeach
</table>
@endif
@endsection