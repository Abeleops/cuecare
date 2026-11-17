<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        DB::table('roles')->upsert([
            [
                'name' => 'Administrator',
                'description' => 'Manages staff, products, customers, sales, and reports',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Cashier',
                'description' => 'Processes customer orders and POS transactions',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ], ['name'], ['description', 'updated_at']);
    }
}
