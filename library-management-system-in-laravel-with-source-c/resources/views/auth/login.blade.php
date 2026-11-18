@extends('layout')
@section('title', 'Log in')

@section('content')
<form class="card login" method="post" action="{{ route('login') }}">
    @csrf
    <h1>Book<b>Nest</b></h1>
    <p class="muted">Library management system</p>
    <label>Email
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
    </label>
    <label>Password
        <input type="password" name="password" required>
    </label>
    <button class="btn">Log in</button>
    <p class="muted small">Demo: demo@example.com / Demo@1234</p>
</form>
@endsection
