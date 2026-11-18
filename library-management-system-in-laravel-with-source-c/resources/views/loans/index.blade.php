@extends('layout')
@section('title', 'Loans')

@section('content')
<div class="bar">
    <h1>Loans</h1>
    <a class="btn {{ $filter === 'active' ? '' : 'ghost' }}" href="{{ route('loans.index', ['show' => 'active']) }}">On loan</a>
    <a class="btn {{ $filter === 'overdue' ? '' : 'ghost' }}" href="{{ route('loans.index', ['show' => 'overdue']) }}">Overdue</a>
    <a class="btn {{ $filter === 'all' ? '' : 'ghost' }}" href="{{ route('loans.index', ['show' => 'all']) }}">All</a>
</div>

<div class="card">
    <table>
        <tr><th>Book</th><th>Member</th><th>Borrowed</th><th>Due</th><th>Returned</th><th class="num">Fine</th></tr>
        @forelse ($loans as $loan)
            <tr>
                <td>{{ $loan->book->title }}</td>
                <td>{{ $loan->member->name }}</td>
                <td>{{ $loan->borrowed_on->format('M j') }}</td>
                <td>{{ $loan->due_on->format('M j') }}</td>
                <td>{{ $loan->returned_on?->format('M j') ?? '-' }}</td>
                <td class="num">
                    @if ($loan->returned_on)
                        {{ $loan->fine > 0 ? number_format($loan->fine) : '-' }}
                    @elseif ($loan->daysLate() > 0)
                        <span class="pill late">{{ $loan->currentFine() }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">No loans here.</td></tr>
        @endforelse
    </table>
    {{ $loans->links('partials.pager') }}
</div>
@endsection
