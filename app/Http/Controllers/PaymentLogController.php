<?php

namespace App\Http\Controllers;

use App\Models\PaymentLog;

class PaymentLogController extends Controller
{
    public function index()
    {
        $logs = PaymentLog::with('merchant')
            ->latest()
            ->get();

        return view('payment-logs.index', compact('logs'));
    }
}