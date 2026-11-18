<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Add clearly labelled records for local demonstrations and group testing.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        DB::transaction(function () {
            $timestamp = now();
            $cashierRoleId = DB::table('roles')->where('name', 'Cashier')->value('id');

            DB::table('users')->updateOrInsert(
                ['username' => 'demo_cashier'],
                [
                    'staff_code' => 'CSH-DEMO-001',
                    'role_id' => $cashierRoleId,
                    'full_name' => 'Demo Cashier',
                    'email' => 'cashier@demo.cuecare.local',
                    'password' => Hash::make('demo1234'),
                    'avatar' => null,
                    'is_active' => true,
                    'date_joined' => today(),
                    'remember_token' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'deleted_at' => null,
                ],
            );

            $cashierId = DB::table('users')->where('username', 'demo_cashier')->value('id');

            $customers = [
                [
                    'customer_code' => 'CUST-DEMO-001',
                    'full_name' => 'Demo Customer One',
                    'phone' => '09170000001',
                    'email' => 'customer1@demo.cuecare.local',
                ],
                [
                    'customer_code' => 'CUST-DEMO-002',
                    'full_name' => 'Demo Customer Two',
                    'phone' => '09170000002',
                    'email' => 'customer2@demo.cuecare.local',
                ],
            ];

            foreach ($customers as $customer) {
                DB::table('customers')->updateOrInsert(
                    ['customer_code' => $customer['customer_code']],
                    [
                        ...$customer,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                        'deleted_at' => null,
                    ],
                );
            }

            $products = [
                [
                    'category' => 'Food',
                    'name' => 'Sample Food Item',
                    'price' => 120.00,
                    'starting_stock' => 20,
                    'sold_quantity' => 2,
                ],
                [
                    'category' => 'Beverages',
                    'name' => 'Sample Beverage',
                    'price' => 50.00,
                    'starting_stock' => 30,
                    'sold_quantity' => 1,
                ],
                [
                    'category' => 'Alcoholic Drinks',
                    'name' => 'Sample Alcoholic Drink',
                    'price' => 90.00,
                    'starting_stock' => 24,
                    'sold_quantity' => 2,
                ],
            ];

            foreach ($products as $product) {
                $categoryId = DB::table('categories')->where('name', $product['category'])->value('id');

                DB::table('products')->updateOrInsert(
                    ['name' => $product['name']],
                    [
                        'category_id' => $categoryId,
                        'image' => null,
                        'price' => $product['price'],
                        'stock_quantity' => $product['starting_stock'] - $product['sold_quantity'],
                        'low_stock_threshold' => 10,
                        'is_active' => true,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                        'deleted_at' => null,
                    ],
                );
            }

            $customerOneId = DB::table('customers')->where('customer_code', 'CUST-DEMO-001')->value('id');
            $customerTwoId = DB::table('customers')->where('customer_code', 'CUST-DEMO-002')->value('id');

            $sales = [
                [
                    'transaction_code' => 'TRX-900001',
                    'customer_id' => $customerOneId,
                    'payment_method' => 'cash',
                    'subtotal' => 290.00,
                    'amount_received' => 300.00,
                    'change_amount' => 10.00,
                    'items' => [
                        ['product' => 'Sample Food Item', 'quantity' => 2, 'unit_price' => 120.00],
                        ['product' => 'Sample Beverage', 'quantity' => 1, 'unit_price' => 50.00],
                    ],
                ],
                [
                    'transaction_code' => 'TRX-900002',
                    'customer_id' => $customerTwoId,
                    'payment_method' => 'gcash',
                    'gcash_reference' => 'DEMO1234',
                    'subtotal' => 180.00,
                    'amount_received' => 180.00,
                    'change_amount' => 0.00,
                    'items' => [
                        ['product' => 'Sample Alcoholic Drink', 'quantity' => 2, 'unit_price' => 90.00],
                    ],
                ],
            ];

            $demoSaleIds = DB::table('sales')
                ->whereIn('transaction_code', collect($sales)->pluck('transaction_code'))
                ->pluck('id');

            if ($demoSaleIds->isNotEmpty()) {
                $demoSaleItemIds = DB::table('sale_items')
                    ->whereIn('sale_id', $demoSaleIds)
                    ->pluck('id');

                DB::table('stock_movements')->whereIn('sale_item_id', $demoSaleItemIds)->delete();
                DB::table('sale_items')->whereIn('sale_id', $demoSaleIds)->delete();
            }

            $demoProductIds = DB::table('products')
                ->whereIn('name', collect($products)->pluck('name'))
                ->pluck('id');

            DB::table('stock_movements')
                ->whereIn('product_id', $demoProductIds)
                ->where('notes', 'like', 'Demo:%')
                ->delete();

            foreach ($sales as $sale) {
                DB::table('sales')->updateOrInsert(
                    ['transaction_code' => $sale['transaction_code']],
                    [
                        'customer_id' => $sale['customer_id'],
                        'sold_by' => $cashierId,
                        'subtotal' => $sale['subtotal'],
                        'discount_amount' => 0,
                        'total_amount' => $sale['subtotal'],
                        'payment_method' => $sale['payment_method'],
                        'gcash_reference' => $sale['gcash_reference'] ?? null,
                        'amount_received' => $sale['amount_received'],
                        'change_amount' => $sale['change_amount'],
                        'status' => 'completed',
                        'notes' => 'Demo transaction for group testing.',
                        'completed_at' => $timestamp,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ],
                );

                $saleId = DB::table('sales')->where('transaction_code', $sale['transaction_code'])->value('id');

                foreach ($sale['items'] as $item) {
                    $product = DB::table('products')->where('name', $item['product'])->first();
                    $itemSubtotal = $item['quantity'] * $item['unit_price'];

                    $saleItemId = DB::table('sale_items')->insertGetId([
                        'sale_id' => $saleId,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $itemSubtotal,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);

                    DB::table('stock_movements')->insert([
                        'product_id' => $product->id,
                        'sale_item_id' => $saleItemId,
                        'recorded_by_user_id' => $cashierId,
                        'movement_type' => 'sale',
                        'quantity_change' => -$item['quantity'],
                        'resulting_stock' => $product->stock_quantity,
                        'notes' => "Demo: {$sale['transaction_code']} sale deduction.",
                        'created_at' => $timestamp,
                    ]);
                }
            }

            foreach ($products as $product) {
                $productId = DB::table('products')->where('name', $product['name'])->value('id');

                DB::table('stock_movements')->insert([
                    'product_id' => $productId,
                    'sale_item_id' => null,
                    'recorded_by_user_id' => $cashierId,
                    'movement_type' => 'restock',
                    'quantity_change' => $product['starting_stock'],
                    'resulting_stock' => $product['starting_stock'],
                    'notes' => 'Demo: opening stock for group testing.',
                    'created_at' => $timestamp->copy()->subMinute(),
                ]);
            }
        });
    }
}
