<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            Schema::table('sales', function (Blueprint $table): void {
                $table->decimal('cash_amount', 12, 2)->nullable();
                $table->decimal('gcash_amount', 12, 2)->nullable();
            });
            DB::statement('ALTER TABLE sales DROP CONSTRAINT sales_payment_method_check');
            DB::statement("ALTER TABLE sales ADD CONSTRAINT sales_payment_method_check CHECK (payment_method IN ('cash', 'gcash', 'split'))");
            DB::statement('ALTER TABLE sales DROP CONSTRAINT sales_gcash_reference_check');
            DB::statement("ALTER TABLE sales ADD CONSTRAINT sales_gcash_reference_check CHECK (payment_method NOT IN ('gcash', 'split') OR gcash_reference IS NOT NULL)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            throw new RuntimeException('Split payment records cannot be safely converted back to a single payment method.');
        });
    }
};
