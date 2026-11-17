<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();
        $categories = [
            ['name' => 'Food', 'category_group' => 'food'],
            ['name' => 'Beverages', 'category_group' => 'beverage'],
            ['name' => 'Alcoholic Drinks', 'category_group' => 'alcoholic'],
        ];

        $rows = collect($categories)
            ->values()
            ->map(fn (array $category, int $index) => [
                ...$category,
                'description' => null,
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ])
            ->all();

        DB::table('categories')->upsert(
            $rows,
            ['name'],
            ['category_group', 'sort_order', 'is_active', 'updated_at', 'deleted_at'],
        );
    }
}
