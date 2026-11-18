<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('show', 'active');

        $loans = Loan::with(['book', 'member'])
            ->when($filter === 'active', fn ($q) => $q->active())
            ->when($filter === 'overdue', fn ($q) => $q->overdue())
            ->latest('id')
            ->simplePaginate(10)
            ->withQueryString();

        return view('loans.index', compact('loans', 'filter'));
    }
}
