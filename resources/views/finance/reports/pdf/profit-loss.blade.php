@extends('finance.reports.pdf.layout')

@section('content')
<table class="main-table">
    <tr class="section-header">
        <td colspan="3">REVENUE</td>
    </tr>
    <tr>
        <th>Category</th>
        <th class="text-right">Amount (NPR)</th>
        <th class="text-right">Percentage</th>
    </tr>
    @foreach($report['revenue']['by_category'] as $item)
    <tr>
        <td class="indent-1">{{ $item['category'] }}</td>
        <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
        <td class="text-right">{{ number_format($item['percentage'], 2) }}%</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td><strong>Total Revenue</strong></td>
        <td class="text-right"><strong>{{ number_format($report['revenue']['total'], 2) }}</strong></td>
        <td class="text-right"><strong>100%</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="section-header">
        <td colspan="3">EXPENSES</td>
    </tr>
    <tr>
        <th>Category</th>
        <th class="text-right">Amount (NPR)</th>
        <th class="text-right">Percentage</th>
    </tr>
    @foreach($report['expenses']['by_category'] as $item)
    <tr>
        <td class="indent-1">{{ $item['category'] }}</td>
        <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
        <td class="text-right">{{ number_format($item['percentage'], 2) }}%</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td><strong>Total Expenses</strong></td>
        <td class="text-right"><strong>{{ number_format($report['expenses']['total'], 2) }}</strong></td>
        <td class="text-right"><strong>100%</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="grand-total-row">
        <td><strong>NET PROFIT / (LOSS)</strong></td>
        <td class="text-right">
            <strong class="{{ $report['net_profit'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                {{ number_format($report['net_profit'], 2) }}
            </strong>
        </td>
        <td class="text-right"><strong>{{ number_format($report['profit_margin'], 2) }}%</strong></td>
    </tr>
</table>
@endsection