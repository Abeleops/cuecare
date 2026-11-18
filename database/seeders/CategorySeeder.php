<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();
        $roots = [
            ['name' => 'Food', 'category_group' => 'food', 'parent' => null],
            ['name' => 'Beverages', 'category_group' => 'beverage', 'parent' => null],
            ['name' => 'Alcoholic Drinks', 'category_group' => 'alcoholic', 'parent' => null],
        ];
        $children = [
            'Food' => [
                'Silog Meals',
                'Rice Meals',
                'Chicken Wings',
                'Chicken Poppers',
                'Pulutan',
                'Snacks & Appetizers',
                'Extras & Add-ons',
            ],
            'Beverages' => [
                'Softdrinks & Refreshments',
                'Frappe',
                'Fruity Soda',
                'GG Tower',
            ],
            'Alcoholic Drinks' => ['Beer', 'Hard Liquors', 'Brandy'],
        ];

        // Reuse existing categories when the seeder runs again
        $upsert = function (array $categories, ?int $parentId = null) use ($timestamp): void {
            $rows = collect($categories)->values()->map(fn (array $category, int $index) => [
                'name' => $category['name'],
                'category_group' => $category['category_group'],
                'description' => null,
                'parent_id' => $parentId,
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ])->all();
            DB::table('categories')->upsert($rows, ['name'], [
                'category_group',
                'parent_id',
                'sort_order',
                'is_active',
                'updated_at',
                'deleted_at',
            ]);
        };
        $upsert($roots);
        foreach ($children as $parent => $names) {
            $parentRow = collect($roots)->firstWhere('name', $parent);
            $categories = array_map(fn (string $name) => [
                'name' => $name,
                'category_group' => $parentRow['category_group'],
            ], $names);
            $parentId = (int) DB::table('categories')->where('name', $parent)->value('id');
            $upsert($categories, $parentId);
        }
    }
}
