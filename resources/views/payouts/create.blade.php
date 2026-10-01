<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initiate Payout</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- Sidebar -->
    <div class="bg-dark text-white p-3" style="width: 240px; min-height: 100vh;">

        <h4 class="mb-4">Payment System</h4>

        <div class="nav flex-column">

            <a href="{{ url('/') }}" class="nav-link text-white mb-2">
                Dashboard
            </a>

            <a href="{{ route('merchants.index') }}" class="nav-link text-white mb-2">
                Merchants
            </a>

            <a href="{{ route('pay-ins.index') }}" class="nav-link text-white mb-2">
                Pay-ins
            </a>

            <a href="{{ route('payouts.index') }}" class="nav-link text-white mb-2">
                Payouts
            </a>

            <a href="{{ route('payment-logs.index') }}" class="nav-link text-white mb-2">
                Payment Logs
            </a>

        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4">

        <div class="mb-4">
            <h2 class="fw-bold">Initiate Payout</h2>
            <p class="text-muted">
                Create a new outgoing payment transaction
            </p>
        </div>

        <div class="card shadow-sm border-0" style="max-width: 700px;">

            <div class="card-body p-4">

               <form method="POST" action="{{ route('payouts.store') }}">

                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Merchant
                        </label>

                        <select name="merchant_id" class="form-select" required>

                            <option value="">Select Merchant</option>

                            @foreach($merchants as $merchant)

                                <option value="{{ $merchant->id }}">
                                    {{ $merchant->name }} - {{ $merchant->email }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Amount
                        </label>

                        <input
                            type="number"
                            name="amount"
                            class="form-control"
                            min="1"
                            step="0.01"
                            placeholder="Enter amount"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Customer Reference
                        </label>

                        <input
                            type="text"
                            name="customer_reference"
                            class="form-control"
                            placeholder="Example: PAYOUT-1001"
                        >
                    </div>

                    <div class="d-flex gap-2">

                        <button type="submit" class="btn btn-primary">
                            Initiate Payout
                        </button>

                        <a href="{{ route('payouts.index') }}"
                           class="btn btn-outline-secondary">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</body>
</html>