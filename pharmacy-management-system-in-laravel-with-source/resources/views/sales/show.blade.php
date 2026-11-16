@extends('layout')
@section('title', $sale->receipt_no)

@section('content')
<div class="card receipt">
    <h1>Pharma<b>Care</b></h1>
    <p class="muted">{{ $sale->receipt_no }} · {{ $sale->created_at->format('M j, Y g:i a') }}<br>
        Cashier: {{ $sale->user->name }}</p>
    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td>{{ $item->medicine->name }} × {{ $item->quantity }}</td>
                <td class="num">{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total"><td>Total</td><td class="num">{{ number_format($sale->total, 2) }}</td></tr>
        <tr><td>Paid</td><td class="num">{{ number_format($sale->paid, 2) }}</td></tr>
        <tr><td>Change</td><td class="num">{{ number_format($sale->paid - $sale->total, 2) }}</td></tr>
    </table>
    <a class="btn ghost" href="{{ route('pos') }}">New sale</a>
</div>
@endsection
