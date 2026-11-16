@extends('layout')
@section('title', 'Dashboard')

@section('content')
<h1>Dashboard</h1>

<div class="stats">
    <div class="card stat"><span>{{ number_format($todaySales, 2) }}</span>Sales today ({{ $todayCount }})</div>
    <div class="card stat"><span>{{ $medicineCount }}</span>Medicines</div>
    <div class="card stat bad"><span>{{ $expired->count() }}</span>Expired</div>
    <div class="card stat warn"><span>{{ $expiring->count() }}</span>Expiring in 60 days</div>
    <div class="card stat warn"><span>{{ $lowStock->count() }}</span>Low stock</div>
</div>

<div class="cols">
    <section class="card">
        <h2>Expired: remove from shelf</h2>
        @include('partials.alert-table', ['rows' => $expired, 'kind' => 'expired'])
    </section>
    <section class="card">
        <h2>Expiring soon</h2>
        @include('partials.alert-table', ['rows' => $expiring, 'kind' => 'expiring'])
    </section>
    <section class="card">
        <h2>Low stock: reorder</h2>
        @include('partials.alert-table', ['rows' => $lowStock, 'kind' => 'low'])
    </section>
</div>
@endsection
