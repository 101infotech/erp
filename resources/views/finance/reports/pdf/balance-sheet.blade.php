@extends('finance.reports.pdf.layout')

@section('content')
<table class="main-table">
    <tr class="section-header">
        <td colspan="2">ASSETS</td>
    </tr>

    <tr class="subsection-header">
        <td colspan="2">Current Assets</td>
    </tr>
    @foreach($report['assets']['current_assets'] as $asset)
    <tr>
        <td class="indent-1">{{ $asset['account'] }}</td>
        <td class="text-right">{{ number_format($asset['balance'], 2) }}</td>
    </tr>
    @endforeach

    <tr class="subsection-header">
        <td colspan="2">Fixed Assets</td>
    </tr>
    @foreach($report['assets']['fixed_assets'] as $asset)
    <tr>
        <td class="indent-1">{{ $asset['account'] }}</td>
        <td class="text-right">{{ number_format($asset['balance'], 2) }}</td>
    </tr>
    @endforeach

    @if(!empty($report['assets']['other_assets']))
    <tr class="subsection-header">
        <td colspan="2">Other Assets</td>
    </tr>
    @foreach($report['assets']['other_assets'] as $asset)
    <tr>
        <td class="indent-1">{{ $asset['account'] }}</td>
        <td class="text-right">{{ number_format($asset['balance'], 2) }}</td>
    </tr>
    @endforeach
    @endif

    <tr class="total-row">
        <td><strong>TOTAL ASSETS</strong></td>
        <td class="text-right"><strong>{{ number_format($report['assets']['total_assets'], 2) }}</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="section-header">
        <td colspan="2">LIABILITIES</td>
    </tr>

    <tr class="subsection-header">
        <td colspan="2">Current Liabilities</td>
    </tr>
    @foreach($report['liabilities']['current_liabilities'] as $liability)
    <tr>
        <td class="indent-1">{{ $liability['account'] }}</td>
        <td class="text-right">{{ number_format($liability['balance'], 2) }}</td>
    </tr>
    @endforeach

    @if(!empty($report['liabilities']['long_term_liabilities']))
    <tr class="subsection-header">
        <td colspan="2">Long-term Liabilities</td>
    </tr>
    @foreach($report['liabilities']['long_term_liabilities'] as $liability)
    <tr>
        <td class="indent-1">{{ $liability['account'] }}</td>
        <td class="text-right">{{ number_format($liability['balance'], 2) }}</td>
    </tr>
    @endforeach
    @endif

    <tr class="total-row">
        <td><strong>TOTAL LIABILITIES</strong></td>
        <td class="text-right"><strong>{{ number_format($report['liabilities']['total_liabilities'], 2) }}</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="section-header">
        <td colspan="2">EQUITY</td>
    </tr>
    @foreach($report['equity']['capital'] as $equity)
    <tr>
        <td class="indent-1">{{ $equity['account'] }}</td>
        <td class="text-right">{{ number_format($equity['balance'], 2) }}</td>
    </tr>
    @endforeach
    <tr>
        <td class="indent-1">Retained Earnings</td>
        <td class="text-right">{{ number_format($report['equity']['retained_earnings'], 2) }}</td>
    </tr>
    <tr class="total-row">
        <td><strong>TOTAL EQUITY</strong></td>
        <td class="text-right"><strong>{{ number_format($report['equity']['total_equity'], 2) }}</strong></td>
    </tr>
</table>

<table class="main-table">
    <tr class="grand-total-row">
        <td><strong>TOTAL LIABILITIES & EQUITY</strong></td>
        <td class="text-right"><strong>{{ number_format($report['total_liabilities_and_equity'], 2) }}</strong></td>
    </tr>
    <tr>
        <td colspan="2" class="text-center">
            <span class="status-badge {{ $report['balance_check'] ? 'status-balanced' : 'status-unbalanced' }}">
                {{ $report['balance_check'] ? 'BALANCED ✓' : 'NOT BALANCED ✗' }}
            </span>
        </td>
    </tr>
</table>
@endsection