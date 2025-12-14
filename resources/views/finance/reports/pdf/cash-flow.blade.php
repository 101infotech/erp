@extends('finance.reports.pdf.layout')

@section('content')
<table class="main-table">
    <tr class="section-header">
        <td colspan="2">OPERATING ACTIVITIES</td>
    </tr>

    @if(!empty($report['operating_activities']['inflows']))
    <tr class="subsection-header">
        <td colspan="2">Cash Inflows</td>
    </tr>
    @foreach($report['operating_activities']['inflows'] as $item)
    <tr>
        <td class="indent-1">{{ $item['description'] }}</td>
        <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
    </tr>
    @endforeach
    @endif

    @if(!empty($report['operating_activities']['outflows']))
    <tr class="subsection-header">
        <td colspan="2">Cash Outflows</td>
    </tr>
    @foreach($report['operating_activities']['outflows'] as $item)
    <tr>
        <td class="indent-1">{{ $item['description'] }}</td>
        <td class="text-right">({{ number_format($item['amount'], 2) }})</td>
    </tr>
    @endforeach
    @endif

    <tr class="total-row">
        <td><strong>Net Operating Cash Flow</strong></td>
        <td class="text-right"><strong>{{ number_format($report['operating_activities']['net'], 2) }}</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="section-header">
        <td colspan="2">INVESTING ACTIVITIES</td>
    </tr>

    @if(!empty($report['investing_activities']['inflows']))
    <tr class="subsection-header">
        <td colspan="2">Cash Inflows</td>
    </tr>
    @foreach($report['investing_activities']['inflows'] as $item)
    <tr>
        <td class="indent-1">{{ $item['description'] }}</td>
        <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
    </tr>
    @endforeach
    @endif

    @if(!empty($report['investing_activities']['outflows']))
    <tr class="subsection-header">
        <td colspan="2">Cash Outflows</td>
    </tr>
    @foreach($report['investing_activities']['outflows'] as $item)
    <tr>
        <td class="indent-1">{{ $item['description'] }}</td>
        <td class="text-right">({{ number_format($item['amount'], 2) }})</td>
    </tr>
    @endforeach
    @endif

    <tr class="total-row">
        <td><strong>Net Investing Cash Flow</strong></td>
        <td class="text-right"><strong>{{ number_format($report['investing_activities']['net'], 2) }}</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="section-header">
        <td colspan="2">FINANCING ACTIVITIES</td>
    </tr>

    @if(!empty($report['financing_activities']['inflows']))
    <tr class="subsection-header">
        <td colspan="2">Cash Inflows</td>
    </tr>
    @foreach($report['financing_activities']['inflows'] as $item)
    <tr>
        <td class="indent-1">{{ $item['description'] }}</td>
        <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
    </tr>
    @endforeach
    @endif

    @if(!empty($report['financing_activities']['outflows']))
    <tr class="subsection-header">
        <td colspan="2">Cash Outflows</td>
    </tr>
    @foreach($report['financing_activities']['outflows'] as $item)
    <tr>
        <td class="indent-1">{{ $item['description'] }}</td>
        <td class="text-right">({{ number_format($item['amount'], 2) }})</td>
    </tr>
    @endforeach
    @endif

    <tr class="total-row">
        <td><strong>Net Financing Cash Flow</strong></td>
        <td class="text-right"><strong>{{ number_format($report['financing_activities']['net'], 2) }}</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="section-header">
        <td colspan="2">CASH FLOW SUMMARY</td>
    </tr>
    <tr>
        <td>Opening Cash Balance</td>
        <td class="text-right">{{ number_format($report['opening_cash_balance'], 2) }}</td>
    </tr>
    <tr>
        <td>Net Cash Flow</td>
        <td class="text-right {{ $report['net_cash_flow'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
            {{ number_format($report['net_cash_flow'], 2) }}
        </td>
    </tr>
    <tr class="grand-total-row">
        <td><strong>Closing Cash Balance</strong></td>
        <td class="text-right"><strong>{{ number_format($report['closing_cash_balance'], 2) }}</strong></td>
    </tr>
</table>
@endsection