<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchants</title>

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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Merchants</h2>

                <a href="{{ route('merchants.create') }}" class="btn btn-primary">
                    + Create Merchant
                </a>
            </div>
            <p class="text-muted">Manage and view merchant accounts</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>API Key</th>
                                <th>Status</th>
                                <th>Wallet Balance</th>
                                <th>Created</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($merchants as $merchant)

                                <tr>
                                    <td>{{ $merchant->id }}</td>

                                    <td class="fw-semibold">
                                        {{ $merchant->name }}
                                    </td>

                                    <td>
                                        {{ $merchant->email }}
                                    </td>

                                    <td>
                                        <code>{{ $merchant->api_key }}</code>
                                    </td>

                                    <td>
                                        @if($merchant->status === 'ACTIVE')
                                            <span class="badge bg-success">
                                                ACTIVE
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                INACTIVE
                                            </span>
                                        @endif
                                    </td>

                                    <td class="fw-semibold">
                                        ₹{{ number_format($merchant->wallet?->balance ?? 0, 2) }}
                                    </td>

                                    <td>
                                        {{ $merchant->created_at->format('d M Y') }}
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No merchants found.
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

</body>
</html>