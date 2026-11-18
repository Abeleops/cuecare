<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $sale->transaction_code }}</title>
    @vite(['resources/css/sales.css'])
</head>
<body>
    <main class="receiptPage">
        <article class="receiptCard">
            <header class="receiptHeader">
                <span class="receiptEyebrow">GG Billiards &amp; Autocare</span>
                <h1>Official Receipt</h1>
                <p>{{ $sale->transaction_code }}</p>
                <p>{{ optional($sale->completed_at)->format('M d, Y h:i A') }}</p>
            </header>

            <dl class="receiptMeta">
                <div>
                    <dt>Cashier</dt>
                    <dd>{{ $sale->soldBy?->full_name }}</dd>
                </div>
                <div>
                    <dt>Payment</dt>
                    <dd>{{ strtoupper($sale->payment_method) }}</dd>
                </div>
                <div>
                    <dt>Status</dt>
                    <dd>{{ ucfirst($sale->status) }}</dd>
                </div>
                @if ($sale->payment_method === 'split')
                    <div><dt>Cash</dt><dd>₱{{ number_format((float) $sale->cash_amount, 2) }}</dd></div>
                    <div><dt>GCash</dt><dd>₱{{ number_format((float) $sale->gcash_amount, 2) }}</dd></div>
                @endif
            </dl>

            <table class="salesItems receiptItems">
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->product_name }} × {{ $item->quantity }}</td>
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
                <button type="button" onclick="window.print()">Print receipt</button>
                <a class="secondary" href="{{ route('sales.show', $sale) }}">Back</a>
            </div>
        </article>
    </main>
</body>
</html>
