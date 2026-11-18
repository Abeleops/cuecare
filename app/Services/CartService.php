<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class CartService
{
    public function snapshot(Request $request): array
    {
        $quantities = $request->session()->get('pos.cart', []);
        $products = Product::query()
            ->where('is_active', true)
            ->whereIn('id', array_keys($quantities))
            ->get();
        $items = [];
        $subtotal = 0;
        // Calculate cart amounts in cents to avoid rounding each line twice
        foreach ($products as $product) {
            $quantity = (int) $quantities[$product->id];
            $unitPriceCents = (int) round((float) $product->price * 100);
            $lineTotalCents = $unitPriceCents * $quantity;
            $subtotal += $lineTotalCents;
            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => '₱'.number_format($unitPriceCents / 100, 2),
                'subtotal' => '₱'.number_format($lineTotalCents / 100, 2),
            ];
        }

        $discount = $request->session()->get('pos.discount', ['type' => 'fixed', 'value' => 0]);
        $discountCents = $discount['type'] === 'percentage'
            ? (int) round($subtotal * (float) $discount['value'] / 100)
            : (int) round((float) $discount['value'] * 100);

        return [
            'items' => $items,
            'subtotal_cents' => $subtotal,
            'subtotal' => '₱'.number_format($subtotal / 100, 2),
            'discount_cents' => $discountCents,
            'discount' => '₱'.number_format($discountCents / 100, 2),
            'total' => '₱'.number_format(($subtotal - $discountCents) / 100, 2),
        ];
    }
}
