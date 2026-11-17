<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('staff_code', 20)->unique('users_staff_code_key');
            $table->foreignId('role_id')
                ->constrained('roles')
                ->restrictOnDelete();
            $table->string('full_name', 150);
            $table->string('username', 50)->unique('users_username_key');
            $table->string('email', 150)->nullable()->unique('users_email_key');
            $table->string('password', 255);
            $table->string('avatar', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('date_joined')->default(DB::raw('CURRENT_DATE'));
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->softDeletes();

            $table->index('role_id', 'idx_users_role_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
