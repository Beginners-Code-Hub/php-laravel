<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use DomainException;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function index()
    {
        $recent = Loan::with(['book', 'member'])
            ->latest('id')
            ->take(6)
            ->get();

        return view('counter', compact('recent'));
    }

    public function borrow(Request $request)
    {
        $data = $request->validate([
            'member' => ['required', 'string'],
            'book' => ['required', 'string'],
        ]);

        $member = Member::where('code', trim($data['member']))->first();
        $book = Book::where('code', trim($data['book']))->first();

        if (! $member || ! $book) {
            $what = $member ? 'book' : 'member';

            return back()->with('bad', "Unknown {$what} code.");
        }

        try {
            $loan = $member->borrow($book);
        } catch (DomainException $e) {
            return back()->with('bad', $e->getMessage());
        }

        $due = $loan->due_on->format('M j');

        return back()->with('ok', "Borrowed: {$book->title}. Due {$due}.");
    }

    public function giveBack(Request $request)
    {
        $data = $request->validate(['book' => ['required', 'string']]);
        $code = trim($data['book']);

        $loan = Loan::active()
            ->whereHas('book', fn ($q) => $q->where('code', $code))
            ->with('book')
            ->first();

        if (! $loan) {
            return back()->with('bad', 'That book is not on loan.');
        }

        $loan->giveBack();

        $note = $loan->fine > 0
            ? "Fine: {$loan->fine} ({$loan->daysLate()} days late)."
            : 'On time, no fine.';

        return back()->with('ok', "Returned: {$loan->book->title}. {$note}");
    }
}
