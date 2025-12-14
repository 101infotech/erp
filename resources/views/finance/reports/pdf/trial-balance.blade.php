@extends('finance.reports.pdf.layout')

@section('content')
<table class="main-table">
    <tr>
        <th>Account</th>
        <th>Type</th>
        <th class="text-right">Debit</th>
        <th class="text-right">Credit</th>
        <th class="text-right">Balance</th>
    </tr>
    @foreach($report['accounts'] as $account)
    <tr>
        <td>{{ $account['account'] }}</td>
        <td>{{ ucfirst($account['account_type']) }}</td>
        <td class="text-right">{{ number_format($account['debit'], 2) }}</td>
        <td class="text-right">{{ number_format($account['credit'], 2) }}</td>
        <td class="text-right">{{ number_format($account['balance'], 2) }}</td>
    </tr>
    @endforeach
    <tr class="grand-total-row">
        <td colspan="2"><strong>TOTALS</strong></td>
        <td class="text-right"><strong>{{ number_format($report['totals']['total_debits'], 2) }}</strong></td>
        <td class="text-right"><strong>{{ number_format($report['totals']['total_credits'], 2) }}</strong></td>
        <td class="text-right"><strong>{{ number_format($report['totals']['difference'], 2) }}</strong></td>
    </tr>
    <tr>
        <td colspan="5" class="text-center">
            <span class="status-badge {{ $report['balanced'] ? 'status-balanced' : 'status-unbalanced' }}">
                {{ $report['balanced'] ? 'BALANCED ✓' : 'NOT BALANCED ✗' }}
            </span>
        </td>
    </tr>
</table>
@endsection