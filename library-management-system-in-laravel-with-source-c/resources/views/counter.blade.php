@extends('layout')
@section('title', 'Counter')

@section('content')
<h1>Counter</h1>

<div class="counter">
    <form class="card" method="post" action="{{ route('counter.borrow') }}">
        @csrf
        <h2>Borrow</h2>
        <label>1. Scan the member card
            <input class="scan" name="member" id="member" value="{{ old('member') }}" placeholder="MB-1001" autocomplete="off" autofocus>
        </label>
        <label>2. Scan the book
            <input class="scan" name="book" id="book" placeholder="BK-0001" autocomplete="off">
        </label>
        <button class="btn">Borrow</button>
    </form>

    <form class="card" method="post" action="{{ route('counter.return') }}">
        @csrf
        <h2>Return</h2>
        <label>Scan the book
            <input class="scan" name="book" placeholder="BK-0001" autocomplete="off">
        </label>
        <button class="btn ghost">Return</button>
    </form>
</div>

<div class="card">
    <h2>Latest activity</h2>
    <table>
        <tr><th>Book</th><th>Member</th><th>Borrowed</th><th>Due</th><th>Status</th></tr>
        @foreach ($recent as $loan)
            <tr>
                <td>{{ $loan->book->title }}</td>
                <td>{{ $loan->member->name }}</td>
                <td>{{ $loan->borrowed_on->format('M j') }}</td>
                <td>{{ $loan->due_on->format('M j') }}</td>
                <td>
                    @if ($loan->returned_on)
                        <span class="pill in">Returned</span>
                    @elseif ($loan->daysLate() > 0)
                        <span class="pill late">Overdue</span>
                    @else
                        <span class="pill out">On loan</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection

@push('scripts')
<script>
    // A barcode/QR scanner types the code and presses Enter: jump to the next box.
    document.getElementById('member').addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('book').focus(); }
    });
</script>
@endpush
