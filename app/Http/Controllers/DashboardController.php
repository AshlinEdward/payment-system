<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\PayIn;
use App\Models\Payout;
use App\Models\Wallet;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'merchantCount' => Merchant::count(),
            'payInCount' => PayIn::count(),
            'payoutCount' => Payout::count(),
            'totalWalletBalance' => Wallet::sum('balance'),

            'recentPayIns' => PayIn::latest()->take(5)->get(),
            'recentPayouts' => Payout::latest()->take(5)->get(),
        ]);
    }
}