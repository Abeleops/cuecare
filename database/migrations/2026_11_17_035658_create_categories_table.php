<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique('categories_name_key');
            $table->string('category_group', 20);
            $table->string('description', 255)->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();
        });

        DB::statement(
            "ALTER TABLE categories
             ADD CONSTRAINT categories_group_check
             CHECK (category_group IN ('food', 'beverage', 'alcoholic'))"
        );

        DB::statement(
            'ALTER TABLE categories
             ADD CONSTRAINT categories_sort_order_check
             CHECK (sort_order >= 0)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
