@extends('layout')
@section('title', 'Dashboard')

@section('content')
<h1>Dashboard</h1>

<div class="stats">
    <div class="card stat">Books <span>{{ $books }}</span></div>
    <div class="card stat">Members <span>{{ $members }}</span></div>
    <div class="card stat warn">On loan <span>{{ $onLoan }}</span></div>
    <div class="card stat bad">Overdue <span>{{ $overdue->count() }}</span></div>
</div>

<div class="cols two">
    <div class="card">
        <h2>Overdue now</h2>
        @include('partials.loan-table', ['rows' => $overdue, 'empty' => 'Nothing overdue. 🎉'])
    </div>
    <div class="card">
        <h2>Due in the next 3 days</h2>
        @include('partials.loan-table', ['rows' => $dueSoon, 'empty' => 'No books due soon.'])
    </div>
</div>
@endsection
