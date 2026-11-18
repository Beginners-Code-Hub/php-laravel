@extends('layout')
@section('title', 'Member card')

@section('content')
<div class="card card-id" style="margin:0 auto">
    <h2>Book<b>Nest</b> library card</h2>
    {!! \App\Support\Qr::svg($member->code, 200) !!}
    <h1>{{ $member->name }}</h1>
    <p class="mono">{{ $member->code }}</p>
    <button class="btn" onclick="window.print()">Print card</button>
</div>
@endsection
