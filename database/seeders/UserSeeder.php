<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Manager
        User::create([
            'first_name' => 'Kevin',
            'last_name' => 'Lim',
            'username' => 'kev',
            'mobile_number' => '09171234567',
            'password' => Hash::make('1234'),
            'role' => 'manager',
        ]);

        // Lead
        User::create([
            'first_name' => 'Milo',
            'last_name' => 'Gorospe',
            'username' => 'milo',
            'mobile_number' => '09181234567',
            'password' => Hash::make('1234'),
            'role' => 'lead',
        ]);

        // Content
        User::create([
            'first_name' => 'Angelyn',
            'last_name' => 'Catolico',
            'username' => 'ange',
            'mobile_number' => '09191234567',
            'password' => Hash::make('1234'),
            'role' => 'content',
        ]);

        User::create([
            'first_name' => 'Czein',
            'last_name' => 'Laruscain',
            'username' => 'czein',
            'mobile_number' => '09201234567',
            'password' => Hash::make('1234'),
            'role' => 'content',
        ]);

        User::create([
            'first_name' => 'Jamie',
            'last_name' => 'Ortiz',
            'username' => 'jamie',
            'mobile_number' => '09211234567',
            'password' => Hash::make('1234'),
            'role' => 'content',
        ]);

        User::create([
            'first_name' => 'Well',
            'last_name' => 'Dacoco',
            'username' => 'well',
            'mobile_number' => '09221234567',
            'password' => Hash::make('1234'),
            'role' => 'content',
        ]);

        User::create([
            'first_name' => 'Em',
            'last_name' => 'Delos Santos',
            'username' => 'em',
            'mobile_number' => '09231234567',
            'password' => Hash::make('1234'),
            'role' => 'content',
        ]);

        User::create([
            'first_name' => 'Mark Ivan',
            'last_name' => 'Empleo',
            'username' => 'ivan',
            'mobile_number' => '09241234567',
            'password' => Hash::make('1234'),
            'role' => 'content',
        ]);

        User::create([
            'first_name' => 'Tammy',
            'last_name' => 'Flores',
            'username' => 'tami',
            'mobile_number' => '09251234567',
            'password' => Hash::make('1234'),
            'role' => 'content',
        ]);
    }
}
