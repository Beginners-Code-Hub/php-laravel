@extends('layout')
@section('title', $book->exists ? 'Edit book' : 'Add book')

@section('content')
<h1>{{ $book->exists ? 'Edit book' : 'Add book' }}</h1>

<form class="card form" method="post"
      action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}">
    @csrf
    @if ($book->exists) @method('put') @endif
    <label>Code (printed as a QR on the label)
        <input name="code" value="{{ old('code', $book->code) }}" placeholder="BK-0013" required>
    </label>
    <label>Title
        <input name="title" value="{{ old('title', $book->title) }}" required>
    </label>
    <label>Author
        <input name="author" value="{{ old('author', $book->author) }}" required>
    </label>
    <label>Category
        <input name="category" value="{{ old('category', $book->category) }}" required>
    </label>
    <div class="actions">
        <a class="btn ghost" href="{{ route('books.index') }}">Cancel</a>
        <button class="btn">Save</button>
    </div>
</form>
@endsection
