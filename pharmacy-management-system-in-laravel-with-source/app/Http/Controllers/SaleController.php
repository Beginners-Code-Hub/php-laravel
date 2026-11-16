<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.index', [
            'sales' => Sale::with('user')->latest()->paginate(15),
        ]);
    }

    public function show(Sale $sale)
    {
        return view('sales.show', ['sale' => $sale->load('items.medicine', 'user')]);
    }
}
