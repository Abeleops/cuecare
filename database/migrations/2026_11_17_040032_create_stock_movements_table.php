<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->foreignId('sale_item_id')
                ->nullable()
                ->constrained('sale_items')
                ->nullOnDelete();

            $table->foreignId('recorded_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('movement_type', 20);
            $table->integer('quantity_change');
            $table->integer('resulting_stock');
            $table->string('notes', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['product_id', 'created_at'],
                'idx_stock_movements_product_created_at'
            );

            $table->index(
                'sale_item_id',
                'idx_stock_movements_sale_item_id'
            );

            $table->index(
                'recorded_by_user_id',
                'idx_stock_movements_recorded_by_user_id'
            );
        });

        DB::statement(
            "ALTER TABLE stock_movements
             ADD CONSTRAINT stock_movements_type_check
             CHECK (movement_type IN ('restock', 'sale', 'adjustment', 'return'))"
        );

        DB::statement(
            'ALTER TABLE stock_movements
             ADD CONSTRAINT stock_movements_quantity_change_check
             CHECK (quantity_change <> 0)'
        );

        DB::statement(
            'ALTER TABLE stock_movements
             ADD CONSTRAINT stock_movements_resulting_stock_check
             CHECK (resulting_stock >= 0)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
