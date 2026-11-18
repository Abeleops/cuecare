<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function checkout(array $data): Sale
    {
        return DB::transaction(function () use ($data): Sale {
            $subtotal = 0.0;
            $lockedProducts = [];
            $items = $data['items'];

            // Lock products in the same order to reduce deadlocks
            usort($items, fn (array $left, array $right): int => $left['product_id'] <=> $right['product_id']);
            foreach ($items as $item) {
                $product = Product::query()->whereKey($item['product_id'])->lockForUpdate()->first();
                if (! $product || ! $product->is_active || $product->trashed()) {
                    throw ValidationException::withMessages(['items' => 'A selected product is no longer available.']);
                }
                if ($product->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages(['items' => "Insufficient stock for {$product->name}."]);
                }
                $lineTotal = (float) $product->price * $item['quantity'];
                $subtotal += $lineTotal;
                $lockedProducts[] = [$product, $item['quantity'], $lineTotal];
            }

            // Recalculate discounts and totals from the locked product prices
            $cartDiscount = $data['cart_discount'] ?? ['type' => 'fixed', 'value' => 0];
            $discount = $cartDiscount['type'] === 'percentage'
                ? round($subtotal * (float) $cartDiscount['value'] / 100, 2)
                : round((float) $cartDiscount['value'], 2);
            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Discount cannot exceed subtotal.']);
            }
            if ($discount > 0 && ! auth()->user()->hasPermission('DiscountApply')) {
                throw ValidationException::withMessages(['discount' => 'You are not allowed to apply a discount.']);
            }
            $total = round($subtotal - $discount, 2);
            $received = $data['payment_method'] === 'split'
                ? round((float) $data['cash_amount'] + (float) $data['gcash_amount'], 2)
                : (float) $data['amount_received'];
            if ($data['payment_method'] === 'cash' && $received < $total) {
                throw ValidationException::withMessages(['amount_received' => 'Cash received must cover the total.']);
            }
            if ($data['payment_method'] === 'gcash' && (round($received, 2) !== $total || empty($data['gcash_reference']))) {
                throw ValidationException::withMessages(['amount_received' => 'GCash payment must equal the total and include a reference.']);
            }
            if ($data['payment_method'] === 'split' && $received !== $total) {
                throw ValidationException::withMessages(['cash_amount' => 'Cash and GCash amounts must add up to the total.']);
            }
            $usesGcash = in_array($data['payment_method'], ['gcash', 'split'], true);
            $saleData = [
                'transaction_code' => 'PENDING-'.Str::random(22),
                'customer_id' => $data['customer_id'] ?? null,
                'sold_by' => auth()->id(),
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'payment_method' => $data['payment_method'],
                'gcash_reference' => $usesGcash ? $data['gcash_reference'] : null,
                'order_type' => $data['order_type'],
                'amount_received' => $received,
                'change_amount' => max(0, $received - $total),
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
            ];
            if ($data['payment_method'] === 'split') {
                $saleData['cash_amount'] = $data['cash_amount'];
                $saleData['gcash_amount'] = $data['gcash_amount'];
            }
            $sale = Sale::query()->create($saleData);
            $sale->update(['transaction_code' => 'TRX-'.str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT)]);
            // Save each line and its stock movement in the same transaction
            foreach ($lockedProducts as [$product, $quantity, $lineTotal]) {
                $item = $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $lineTotal,
                ]);
                $product->decrement('stock_quantity', $quantity);
                $product->refresh();
                $product->stockMovements()->create([
                    'sale_item_id' => $item->id,
                    'recorded_by_user_id' => auth()->id(),
                    'movement_type' => 'sale',
                    'quantity_change' => -$quantity,
                    'resulting_stock' => $product->stock_quantity,
                    'notes' => 'Sale '.$sale->transaction_code,
                ]);
            }
            $auditMetadata = ['total' => $total, 'payment_method' => $data['payment_method']];
            // Keep only the end of the payment reference in the audit log
            if ($usesGcash) {
                $auditMetadata['gcash_reference_last_four'] = substr($data['gcash_reference'], -4);
            }
            $this->audit->record('sale_created', $sale, $auditMetadata);

            return $sale;
        });
    }
}
