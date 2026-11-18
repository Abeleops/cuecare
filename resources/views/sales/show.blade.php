<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $sale->transaction_code }}</title>
    @vite(['resources/css/sales.css'])
</head>
<body>
    <main class="salesView">
        <article class="salesCard">
            <span class="salesEyebrow">Sale details</span>
            <h1>{{ $sale->transaction_code }}</h1>

            <dl class="salesMeta">
                <div>
                    <dt>Status</dt>
                    <dd>{{ ucfirst($sale->status) }}</dd>
                </div>
                <div>
                    <dt>Customer</dt>
                    <dd>{{ $sale->customer?->full_name ?: 'Walk-in customer' }}</dd>
                </div>
                <div>
                    <dt>Cashier</dt>
                    <dd>{{ $sale->soldBy?->full_name }}</dd>
                </div>
            </dl>

            <table class="salesItems">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format((float) $item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="salesTotal">
                <span>Total</span>
                <span>₱{{ number_format((float) $sale->total_amount, 2) }}</span>
            </div>

            <div class="salesActions">
                <a href="{{ route('sales.receipt', $sale) }}">View receipt</a>
                <a class="secondary" href="{{ route('sales.index') }}">Back to sales</a>
            </div>

            @can('SalesVoid')
                @if ($sale->status !== 'voided')
                    <form class="voidForm" method="POST" action="{{ route('sales.void', $sale) }}">
                        @csrf
                        <label>
                            Void reason
                            <input name="reason" required maxlength="255">
                        </label>
                        <button type="submit">Void sale</button>
                    </form>
                @endif
            @endcan
        </article>
    </main>
</body>
</html>
