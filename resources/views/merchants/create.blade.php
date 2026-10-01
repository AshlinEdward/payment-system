<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Merchant</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Create Merchant</h2>
            <p class="text-muted mb-0">Add a new merchant to the payment system.</p>
        </div>

        <a href="{{ route('merchants.index') }}" class="btn btn-secondary">
            Back to Merchants
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('merchants.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Merchant Name</label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        placeholder="Enter merchant name"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="merchant@example.com"
                        required
                    >
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Create Merchant
                    </button>

                    <a href="{{ route('merchants.index') }}" class="btn btn-light ms-2">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>