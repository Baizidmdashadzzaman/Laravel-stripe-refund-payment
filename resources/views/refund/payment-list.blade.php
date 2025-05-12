<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payments</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2 class="my-4">Stripe Payments</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Charge ID</th>
                        <th>Payment ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Refund</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->payment_intent }}</td>
                            <td>${{ number_format($payment->amount / 100, 2) }}</td>
                            <td>{{ $payment->status }}</td>
                            <td>{{ \Carbon\Carbon::createFromTimestamp($payment->created)->toDateTimeString() }}</td>
                            <td>
                                @if($payment->refunded)
                                    <span class="badge badge-success">Refunded</span>
                                @else
                                    <form action="{{ route('payments.refund', $payment->id) }}" method="POST" onsubmit="return confirm('Refund this charge?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Refund</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
