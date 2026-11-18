<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $overdue = Loan::overdue()
            ->with(['book', 'member'])
            ->orderBy('due_on')
            ->get();

        $dueSoon = Loan::active()
            ->whereBetween('due_on', [today(), today()->addDays(3)])
            ->with(['book', 'member'])
            ->orderBy('due_on')
            ->get();

        return view('dashboard', [
            'books' => Book::count(),
            'members' => Member::count(),
            'onLoan' => Loan::active()->count(),
            'overdue' => $overdue,
            'dueSoon' => $dueSoon,
        ]);
    }
}
