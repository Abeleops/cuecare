<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropUnique('customers_email_key');
            $table->string('email', 150)->nullable()->change();
        });
        Schema::table('customers', function (Blueprint $table): void {
            $table->string('active_email', 150)->virtualAs('CASE WHEN deleted_at IS NULL THEN email ELSE NULL END')->unique('customers_active_email_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('This migration cannot be safely reversed after an email is reused.');
    }
};
