@extends('layout')
@section('title', 'Sales')

@section('content')
<h1>Sales</h1>
<div class="card">
<table>
    <thead><tr><th>Receipt</th><th>Cashier</th><th>When</th><th class="num">Total</th></tr></thead>
    @forelse ($sales as $s)
        <tr>
            <td><a href="{{ route('sales.show', $s) }}">{{ $s->receipt_no }}</a></td>
            <td>{{ $s->user->name }}</td>
            <td>{{ $s->created_at->format('M j, g:i a') }}</td>
            <td class="num">{{ number_format($s->total, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">No sales yet. Open the Sell screen.</td></tr>
    @endforelse
</table>
</div>
{{ $sales->links('partials.pager') }}
@endsection
