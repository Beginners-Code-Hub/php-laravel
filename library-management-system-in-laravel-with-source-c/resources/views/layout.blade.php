<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BookNest') · BookNest</title>
    <link rel="stylesheet" href="{{ asset('css/booknest.css') }}">
</head>
<body>
@auth
<header class="top">
    <a class="brand" href="{{ route('dashboard') }}">Book<b>Nest</b></a>
    <nav>
        <a href="{{ route('dashboard') }}" @class(['on' => request()->routeIs('dashboard')])>Dashboard</a>
        <a href="{{ route('counter') }}" @class(['on' => request()->routeIs('counter*')])>Counter</a>
        <a href="{{ route('books.index') }}" @class(['on' => request()->routeIs('books.*')])>Books</a>
        <a href="{{ route('members.index') }}" @class(['on' => request()->routeIs('members.*')])>Members</a>
        <a href="{{ route('loans.index') }}" @class(['on' => request()->routeIs('loans.*')])>Loans</a>
    </nav>
    <form method="post" action="{{ route('logout') }}">
        @csrf
        <button class="link">Log out</button>
    </form>
</header>
@endauth
<main>
    @if (session('ok'))
        <div class="result ok">{{ session('ok') }}</div>
    @endif
    @if (session('bad'))
        <div class="result bad">{{ session('bad') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash bad">{{ $errors->first() }}</div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
