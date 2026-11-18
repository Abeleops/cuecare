<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('roles', 'key')) {
            Schema::table('roles', function (Blueprint $table): void {
                $table->string('key', 30)->nullable()->unique()->after('name');
            });
        }
        if (! Schema::hasColumn('categories', 'parent_id')) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            });
        }
        Schema::table('sales', function (Blueprint $table): void {
            if (! Schema::hasColumn('sales', 'gcash_reference')) {
                $table->string('gcash_reference', 50)->nullable()->after('payment_method');
            }
            if (! Schema::hasColumn('sales', 'order_type')) {
                $table->string('order_type', 20)->default('dine_in')->after('gcash_reference');
            }
            if (! Schema::hasColumn('sales', 'void_reason')) {
                $table->string('void_reason', 255)->nullable()->after('status');
            }
        });

        // Preserve historical rows while making the new GCash reference rule valid.
        DB::table('sales')
            ->where('payment_method', 'gcash')
            ->where(function ($query): void {
                $query->whereNull('gcash_reference')->orWhere('gcash_reference', '');
            })
            ->update(['gcash_reference' => 'LEGACY']);

        if (! $this->hasConstraint('sales_order_type_check')) {
            DB::statement("ALTER TABLE sales ADD CONSTRAINT sales_order_type_check CHECK (order_type IN ('dine_in', 'takeout'))");
        }
        if (! $this->hasConstraint('sales_gcash_reference_check')) {
            DB::statement("ALTER TABLE sales ADD CONSTRAINT sales_gcash_reference_check CHECK (payment_method <> 'gcash' OR gcash_reference IS NOT NULL)");
        }

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 100);
                $table->nullableMorphs('subject');
                $table->ipAddress('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    private function hasConstraint(string $name): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'sales')
            ->where('CONSTRAINT_NAME', $name)
            ->exists();
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        DB::statement('ALTER TABLE sales DROP CHECK sales_order_type_check');
        DB::statement('ALTER TABLE sales DROP CHECK sales_gcash_reference_check');
        Schema::table('customers', fn (Blueprint $table) => $table->string('email', 150)->nullable(false)->change());
        Schema::table('sales', fn (Blueprint $table) => $table->dropColumn(['gcash_reference', 'order_type', 'void_reason']));
        Schema::table('categories', fn (Blueprint $table) => $table->dropConstrainedForeignId('parent_id'));
        Schema::table('roles', fn (Blueprint $table) => $table->dropColumn('key'));
    }
};
