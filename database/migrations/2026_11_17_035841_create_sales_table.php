<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 30)->unique('sales_transaction_code_key');

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            $table->foreignId('sold_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method', 20);
            $table->decimal('amount_received', 12, 2);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('completed');
            $table->string('notes', 255)->nullable();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index('customer_id', 'idx_sales_customer_id');
            $table->index('sold_by', 'idx_sales_sold_by');
            $table->index('completed_at', 'idx_sales_completed_at');
        });

        DB::statement(
            "ALTER TABLE sales
             ADD CONSTRAINT sales_payment_method_check
             CHECK (payment_method IN ('cash', 'gcash'))"
        );

        DB::statement(
            "ALTER TABLE sales
             ADD CONSTRAINT sales_status_check
             CHECK (status IN ('completed', 'voided', 'refunded'))"
        );

        DB::statement(
            'ALTER TABLE sales
             ADD CONSTRAINT sales_subtotal_check
             CHECK (subtotal >= 0)'
        );

        DB::statement(
            'ALTER TABLE sales
             ADD CONSTRAINT sales_discount_amount_check
             CHECK (discount_amount >= 0)'
        );

        DB::statement(
            'ALTER TABLE sales
             ADD CONSTRAINT sales_discount_limit_check
             CHECK (discount_amount <= subtotal)'
        );

        DB::statement(
            'ALTER TABLE sales
             ADD CONSTRAINT sales_total_amount_check
             CHECK (total_amount >= 0)'
        );

        DB::statement(
            'ALTER TABLE sales
             ADD CONSTRAINT sales_amount_received_check
             CHECK (amount_received >= 0)'
        );

        DB::statement(
            'ALTER TABLE sales
             ADD CONSTRAINT sales_change_amount_check
             CHECK (change_amount >= 0)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
