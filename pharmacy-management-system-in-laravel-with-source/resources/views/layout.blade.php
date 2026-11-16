<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PharmaCare') · PharmaCare</title>
    <link rel="stylesheet" href="{{ asset('css/pharmacare.css') }}">
</head>
<body>
@auth
<header class="top">
    <a class="brand" href="{{ route('dashboard') }}">Pharma<b>Care</b></a>
    <nav>
        <a href="{{ route('dashboard') }}" @class(['on' => request()->routeIs('dashboard')])>Dashboard</a>
        <a href="{{ route('pos') }}" @class(['on' => request()->routeIs('pos')])>Sell</a>
        <a href="{{ route('medicines.index') }}" @class(['on' => request()->routeIs('medicines.*')])>Medicines</a>
        <a href="{{ route('sales.index') }}" @class(['on' => request()->routeIs('sales.*')])>Sales</a>
    </nav>
    <form method="post" action="{{ route('logout') }}">
        @csrf
        <button class="link">Log out</button>
    </form>
</header>
@endauth
<main>
    @if (session('ok'))
        <div class="flash ok">{{ session('ok') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash bad">{{ $errors->first() }}</div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
