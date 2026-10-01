<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayoutRequest;
use App\Models\Merchant;
use App\Models\Payout;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;

class PayoutController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function index()
    {
        $payouts = Payout::with('merchant')
            ->latest()
            ->get();

        return view('payouts.index', compact('payouts'));
    }

    public function create()
    {
        $merchants = Merchant::where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        return view('payouts.create', compact('merchants'));
    }

    public function store(StorePayoutRequest $request): RedirectResponse
    {
        $this->paymentService->createPayout($request->validated());

        return redirect()
            ->route('payouts.index')
            ->with('success', 'Payout initiated successfully.');
    }
}