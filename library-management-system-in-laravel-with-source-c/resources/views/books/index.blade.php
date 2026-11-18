@extends('layout')
@section('title', 'Books')

@section('content')
<div class="bar">
    <h1>Books</h1>
    <form class="search" method="get">
        <input name="q" value="{{ $q }}" placeholder="Search title, author or code">
        <button class="btn ghost">Search</button>
    </form>
    <a class="btn ghost" href="{{ route('books.labels') }}">QR labels</a>
    <a class="btn" href="{{ route('books.create') }}">+ Add book</a>
</div>

<div class="card">
    <table>
        <tr><th>Code</th><th>Title</th><th>Author</th><th>Category</th><th>Status</th><th></th></tr>
        @forelse ($books as $book)
            <tr>
                <td class="mono">{{ $book->code }}</td>
                <td>{{ $book->title }}</td>
                <td>{{ $book->author }}</td>
                <td>{{ $book->category }}</td>
                <td>
                    @if ($book->activeLoan)
                        <span class="pill out">{{ $book->activeLoan->member->name }}</span>
                    @else
                        <span class="pill in">Available</span>
                    @endif
                </td>
                <td class="num">
                    <a href="{{ route('books.edit', $book) }}">Edit</a>
                    <form method="post" action="{{ route('books.destroy', $book) }}" style="display:inline"
                          onsubmit="return confirm('Delete this book?')">
                        @csrf @method('delete')
                        <button class="link bad">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">No books found.</td></tr>
        @endforelse
    </table>
    {{ $books->links('partials.pager') }}
</div>
@endsection
