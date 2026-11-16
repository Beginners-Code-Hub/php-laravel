@if ($rows->isEmpty())
    <p class="muted">Nothing here. 🎉</p>
@else
    <table>
        @foreach ($rows as $m)
            <tr>
                <td>{{ $m->name }}</td>
                <td class="num">
                    @if ($kind === 'low')
                        {{ $m->stock }} left
                    @else
                        <span class="pill {{ $kind }}">
                            {{ $m->expiry_date->format('M j, Y') }}
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endif
