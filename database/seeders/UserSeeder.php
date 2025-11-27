<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'tenant_id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@mobileshop.com',
            'phone' => '+8801712345678',
            'role' => 'ADMIN',
            'password' => 'password', // Will be hashed by User model mutator
            'is_active' => true,
        ]);

        // Staff User
        User::create([
            'tenant_id' => 1,
            'name' => 'Staff User',
            'email' => 'staff@mobileshop.com',
            'phone' => '+8801787654321',
            'role' => 'STAFF',
            'password' => 'password', // Will be hashed by User model mutator
            'is_active' => true,
        ]);
    }
}
