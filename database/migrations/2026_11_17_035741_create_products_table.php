<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->string('image', 255)->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();

            $table->index('category_id', 'idx_products_category_id');
        });

        DB::statement(
            'ALTER TABLE products
             ADD CONSTRAINT products_price_check
             CHECK (price >= 0)'
        );

        DB::statement(
            'ALTER TABLE products
             ADD CONSTRAINT products_stock_quantity_check
             CHECK (stock_quantity >= 0)'
        );

        DB::statement(
            'ALTER TABLE products
             ADD CONSTRAINT products_low_stock_threshold_check
             CHECK (low_stock_threshold >= 0)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
