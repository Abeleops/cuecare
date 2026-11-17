<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->string('product_name', 150);
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('sale_id', 'idx_sale_items_sale_id');
            $table->index('product_id', 'idx_sale_items_product_id');
        });

        DB::statement(
            'ALTER TABLE sale_items
             ADD CONSTRAINT sale_items_quantity_check
             CHECK (quantity > 0)'
        );

        DB::statement(
            'ALTER TABLE sale_items
             ADD CONSTRAINT sale_items_unit_price_check
             CHECK (unit_price >= 0)'
        );

        DB::statement(
            'ALTER TABLE sale_items
             ADD CONSTRAINT sale_items_total_price_check
             CHECK (total_price >= 0)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
