<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function checkout(array $data): Sale
    {
        return DB::transaction(function () use ($data): Sale {
            $subtotal = 0.0;
            $locked = [];
            foreach ($data['items'] as $item) {
                $product = Product::query()->whereKey($item['product_id'])->lockForUpdate()->first();
                if (! $product || ! $product->is_active || $product->trashed()) {
                    throw ValidationException::withMessages(['items' => 'A selected product is no longer available.']);
                }
                if ($product->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages(['items' => "Insufficient stock for {$product->name}."]);
                }
                $lineTotal = (float) $product->price * $item['quantity'];
                $subtotal += $lineTotal;
                $locked[] = [$product, $item['quantity'], $lineTotal];
            }
            $discount = (float) ($data['discount'] ?? 0);
            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Discount cannot exceed subtotal.']);
            }
            if ($discount > 0 && ! auth()->user()->hasPermission('ReportsView')) {
                throw ValidationException::withMessages(['discount' => 'You are not allowed to apply a discount.']);
            }
            $total = round($subtotal - $discount, 2);
            $received = (float) $data['amount_received'];
            if ($data['payment_method'] === 'cash' && $received < $total) {
                throw ValidationException::withMessages(['amount_received' => 'Cash received must cover the total.']);
            }
            if ($data['payment_method'] === 'gcash' && (round($received, 2) !== $total || empty($data['gcash_reference']))) {
                throw ValidationException::withMessages(['amount_received' => 'GCash payment must equal the total and include a reference.']);
            }
            $sale = Sale::query()->create(['transaction_code' => 'TRX-'.str_pad((string) (Sale::query()->max('id') + 1), 6, '0', STR_PAD_LEFT), 'customer_id' => $data['customer_id'] ?? null, 'sold_by' => auth()->id(), 'subtotal' => $subtotal, 'discount_amount' => $discount, 'total_amount' => $total, 'payment_method' => $data['payment_method'], 'gcash_reference' => $data['payment_method'] === 'gcash' ? substr($data['gcash_reference'], -4) : null, 'order_type' => $data['order_type'], 'amount_received' => $received, 'change_amount' => max(0, $received - $total), 'status' => 'completed', 'notes' => $data['notes'] ?? null]);
            foreach ($locked as [$product, $quantity, $lineTotal]) {
                $item = $sale->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => $quantity, 'unit_price' => $product->price, 'total_price' => $lineTotal]);
                $product->decrement('stock_quantity', $quantity);
                $product->refresh();
                $product->stockMovements()->create(['sale_item_id' => $item->id, 'recorded_by_user_id' => auth()->id(), 'movement_type' => 'sale', 'quantity_change' => -$quantity, 'resulting_stock' => $product->stock_quantity, 'notes' => 'Sale '.$sale->transaction_code]);
            }
            $this->audit->record('sale_created', $sale, ['total' => $total, 'payment_method' => $data['payment_method']]);

            return $sale;
        });
    }
}
