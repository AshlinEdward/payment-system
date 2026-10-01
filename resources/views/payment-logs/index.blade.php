<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Logs</title>

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
            <h2 class="fw-bold">Payment Logs</h2>
            <p class="text-muted">
                Track payment initiation and processing events
            </p>
        </div>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Event</th>
                                <th>Merchant</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Created</th>
                            </tr>
                        </thead>

                        <tbody>

                        @forelse($logs as $log)

                            <tr>

                                <td>
                                    {{ $log->id }}
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $log->event }}
                                    </span>
                                </td>

                                <td>
                                    {{ $log->merchant->name ?? '-' }}
                                </td>

                                <td>

                                    @if($log->pay_in_id)
                                        <span class="badge bg-primary">
                                            PAY-IN
                                        </span>
                                    @elseif($log->payout_id)
                                        <span class="badge bg-info text-dark">
                                            PAYOUT
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            -
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    @if($log->status === 'SUCCESS')

                                        <span class="badge bg-success">
                                            SUCCESS
                                        </span>

                                    @elseif($log->status === 'FAILED')

                                        <span class="badge bg-danger">
                                            FAILED
                                        </span>

                                    @elseif($log->status === 'PENDING')

                                        <span class="badge bg-warning text-dark">
                                            PENDING
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            {{ $log->status ?? '-' }}
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $log->message ?? '-' }}
                                </td>

                                <td>
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No payment logs found.
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
