@extends('layout')
@section('title', 'Members')

@section('content')
<div class="bar">
    <h1>Members</h1>
    <a class="btn" href="{{ route('members.create') }}">+ Add member</a>
</div>

<div class="card">
    <table>
        <tr><th>Code</th><th>Name</th><th>Email</th><th>Phone</th><th class="num">Books out</th><th></th></tr>
        @forelse ($members as $member)
            <tr>
                <td class="mono">{{ $member->code }}</td>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td>{{ $member->phone }}</td>
                <td class="num">{{ $member->active_loans }}</td>
                <td class="num">
                    <a href="{{ route('members.card', $member) }}">Card</a>
                    <a href="{{ route('members.edit', $member) }}">Edit</a>
                    <form method="post" action="{{ route('members.destroy', $member) }}" style="display:inline"
                          onsubmit="return confirm('Delete this member?')">
                        @csrf @method('delete')
                        <button class="link bad">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">No members yet.</td></tr>
        @endforelse
    </table>
    {{ $members->links('partials.pager') }}
</div>
@endsection
