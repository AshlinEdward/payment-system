<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::with('wallet')
            ->latest()
            ->get();

        return view('merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('merchants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:merchants,email'],
        ]);

        $merchant = Merchant::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'api_key' => Str::random(32),
            'status' => 'ACTIVE',
        ]);

        $merchant->wallet()->create([
            'balance' => 0,
        ]);

        return redirect()
            ->route('merchants.index')
            ->with('success', 'Merchant created successfully.');
    }
}