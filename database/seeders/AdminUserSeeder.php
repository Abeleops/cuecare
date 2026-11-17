<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the local administrator account used for development and testing.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $administratorRole = Role::query()
            ->where('name', 'Administrator')
            ->firstOrFail();

        $administrator = User::withTrashed()->firstOrNew([
            'username' => 'test',
        ]);

        $administrator->forceFill([
            'staff_code' => 'ADM-0001',
            'role_id' => $administratorRole->id,
            'full_name' => 'Test Administrator',
            'email' => 'test@cuecare.local',
            'password' => Hash::make('test1234'),
            'avatar' => null,
            'is_active' => true,
            'date_joined' => today(),
            'deleted_at' => null,
        ])->save();
    }
}
