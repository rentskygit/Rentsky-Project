<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@theoutlet.test'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}