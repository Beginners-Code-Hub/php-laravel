@if ($rows->isEmpty())
    <p class="muted">{{ $empty }}</p>
@else
    <table>
        <tr><th>Book</th><th>Member</th><th>Due</th><th class="num">Fine</th></tr>
        @foreach ($rows as $loan)
            <tr>
                <td>{{ $loan->book->title }}</td>
                <td>{{ $loan->member->name }}</td>
                <td>{{ $loan->due_on->format('M j') }}</td>
                <td class="num">
                    @if ($loan->daysLate() > 0)
                        <span class="pill late">{{ $loan->currentFine() }} ({{ $loan->daysLate() }}d)</span>
                    @else
                        <span class="muted">-</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endif
