<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'expired' => Medicine::expired()
                ->orderBy('expiry_date')->get(),
            'expiring' => Medicine::expiringSoon()
                ->orderBy('expiry_date')->get(),
            'lowStock' => Medicine::lowStock()
                ->orderBy('stock')->get(),
            'todaySales' => Sale::whereDate('created_at', today())->sum('total'),
            'todayCount' => Sale::whereDate('created_at', today())->count(),
            'medicineCount' => Medicine::count(),
        ]);
    }
}
