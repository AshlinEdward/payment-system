<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment System Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="d-flex">

    <!-- Sidebar -->
    <div class="bg-dark text-white p-4"
         style="width: 240px; min-height: 100vh;">

        <h4 class="fw-bold mb-4">Payment System</h4>

        <div class="nav flex-column">
            <a href="/"
            class="d-block text-white text-decoration-none py-2">
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
    <main class="flex-grow-1">

        <div class="container-fluid p-4">

            <div class="mb-4">
                <h2 class="fw-bold">Dashboard</h2>

                <p class="text-muted">
                    Overview of your payment system
                </p>
            </div>


            <!-- Statistics -->

            <div class="row g-4">

                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Merchants
                            </h6>

                            <h3 class="fw-bold">
                                {{ $merchantCount }}
                            </h3>

                        </div>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Pay-ins
                            </h6>

                            <h3 class="fw-bold">
                                {{ $payInCount }}
                            </h3>

                        </div>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Payouts
                            </h6>

                            <h3 class="fw-bold">
                                {{ $payoutCount }}
                            </h3>

                        </div>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="text-muted">
                                Total Wallet Balance
                            </h6>

                            <h3 class="fw-bold">
                                ₹{{ number_format($totalWalletBalance, 2) }}
                            </h3>

                        </div>
                    </div>
                </div>

            </div>


            <!-- Recent Transactions -->

            <div class="row g-4 mt-2">


                <!-- Pay-ins -->

                <div class="col-lg-6">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h5 class="fw-bold mb-3">
                                Recent Pay-ins
                            </h5>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead>
                                        <tr>
                                            <th>Transaction</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($recentPayIns as $payIn)

                                            <tr>

                                                <td>
                                                    <small>
                                                        {{ $payIn->transaction_id }}
                                                    </small>
                                                </td>

                                                <td>
                                                    ₹{{ number_format($payIn->amount, 2) }}
                                                </td>

                                                <td>

                                                    <span class="badge
                                                        @if($payIn->status->value === 'SUCCESS')
                                                            bg-success
                                                        @elseif($payIn->status->value === 'FAILED')
                                                            bg-danger
                                                        @else
                                                            bg-warning text-dark
                                                        @endif
                                                    ">
                                                        {{ $payIn->status->value }}
                                                    </span>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="3"
                                                    class="text-center text-muted">
                                                    No pay-ins found.
                                                </td>
                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Payouts -->

                <div class="col-lg-6">

                    <div class="card shadow-sm border-0">

                        <div class="card-body">

                            <h5 class="fw-bold mb-3">
                                Recent Payouts
                            </h5>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead>
                                        <tr>
                                            <th>Transaction</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse($recentPayouts as $payout)

                                            <tr>

                                                <td>
                                                    <small>
                                                        {{ $payout->transaction_id }}
                                                    </small>
                                                </td>

                                                <td>
                                                    ₹{{ number_format($payout->amount, 2) }}
                                                </td>

                                                <td>

                                                    <span class="badge
                                                        @if($payout->status->value === 'SUCCESS')
                                                            bg-success
                                                        @elseif($payout->status->value === 'FAILED')
                                                            bg-danger
                                                        @else
                                                            bg-warning text-dark
                                                        @endif
                                                    ">
                                                        {{ $payout->status->value }}
                                                    </span>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="3"
                                                    class="text-center text-muted">
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

        </div>

    </main>

</div>

</body>
</html>