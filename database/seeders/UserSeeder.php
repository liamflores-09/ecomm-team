<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'System',
            'last_name' => 'Manager',
            'username' => 'manager',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        User::create([
            'first_name' => 'Sample',
            'last_name' => 'User',
            'username' => 'user',
            'password' => Hash::make('password'),
            'role' => 'content',
        ]);
    }
}
