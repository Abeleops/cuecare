<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_code' => fake()->unique()->bothify('STF-####'),
            'role_id' => fn () => Role::query()->firstOrCreate(
                ['name' => 'Cashier'],
                ['description' => 'Processes customer orders and POS transactions'],
            )->id,
            'full_name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'avatar' => null,
            'is_active' => true,
            'date_joined' => today(),
            'remember_token' => Str::random(10),
        ];
    }
}
