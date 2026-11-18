@extends('layout')
@section('title', 'QR labels')

@section('content')
<div class="bar">
    <h1>Book QR labels</h1>
    <button class="btn" onclick="window.print()">Print</button>
</div>

<div class="labels">
    @foreach ($books as $book)
        <div class="card label">
            {!! \App\Support\Qr::svg($book->code, 120) !!}
            <strong class="mono">{{ $book->code }}</strong>
            <span class="muted small">{{ \Illuminate\Support\Str::limit($book->title, 26) }}</span>
        </div>
    @endforeach
</div>
@endsection
