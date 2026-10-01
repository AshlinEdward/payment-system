<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payouts</title>

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
            <h2 class="fw-bold">Payouts</h2>
            <p class="text-muted">
                View all outgoing payment transactions
            </p>
            <a href="{{ route('payouts.create') }}" class="btn btn-primary">
                + Initiate Payout
            </a>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif
        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Transaction ID</th>
                                <th>Merchant</th>
                                <th>Amount</th>
                                <th>Customer Reference</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>

                        <tbody>

                        @forelse($payouts as $payout)

                            <tr>

                                <td>
                                    {{ $payout->id }}
                                </td>

                                <td>
                                    <code>{{ $payout->transaction_id }}</code>
                                </td>

                                <td class="fw-semibold">
                                    {{ $payout->merchant->name ?? '-' }}
                                </td>

                                <td class="fw-semibold">
                                    ₹{{ number_format($payout->amount, 2) }}
                                </td>

                                <td>
                                    {{ $payout->customer_reference ?? '-' }}
                                </td>

                                <td>

                                    @if($payout->status->value === 'SUCCESS')

                                        <span class="badge bg-success">
                                            SUCCESS
                                        </span>

                                    @elseif($payout->status->value === 'FAILED')

                                        <span class="badge bg-danger">
                                            FAILED
                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">
                                            PENDING
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $payout->created_at->format('d M Y H:i') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No payouts found.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>