<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayInRequest;
use App\Models\Merchant;
use App\Models\PayIn;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;

class PayInController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function index()
    {
        $payIns = PayIn::with('merchant')
            ->latest()
            ->get();

        return view('pay-ins.index', compact('payIns'));
    }

    public function create()
    {
        $merchants = Merchant::where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        return view('pay-ins.create', compact('merchants'));
    }

    public function store(StorePayInRequest $request): RedirectResponse
    {
        $this->paymentService->createPayIn($request->validated());

        return redirect()
            ->route('pay-ins.index')
            ->with('success', 'Pay-in initiated successfully.');
    }
}