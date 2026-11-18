<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $books = Book::with('activeLoan.member')
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            }))
            ->orderBy('title')
            ->simplePaginate(10)
            ->withQueryString();

        return view('books.index', compact('books', 'q'));
    }

    public function create()
    {
        $book = new Book(['category' => 'General']);

        return view('books.form', compact('book'));
    }

    public function store(Request $request)
    {
        Book::create($this->validated($request));

        return redirect()->route('books.index')->with('ok', 'Book added.');
    }

    public function edit(Book $book)
    {
        return view('books.form', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $book->update($this->validated($request, $book));

        return redirect()->route('books.index')->with('ok', 'Book updated.');
    }

    public function destroy(Book $book)
    {
        if ($book->onLoan()) {
            return back()->with('bad', 'This book is on loan. Return it first.');
        }

        $book->delete();

        return back()->with('ok', 'Book deleted.');
    }

    public function labels()
    {
        $books = Book::orderBy('code')->get();

        return view('books.labels', compact('books'));
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:books,code,' . $book?->id],
            'title' => ['required', 'string', 'max:150'],
            'author' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:60'],
        ]);
    }
}
