<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['first_name' => 'Mulawin',    'last_name' => 'Galang',        'username' => 'awin',    'mobile_number' => '09001234567', 'role' => 'head'],
            ['first_name' => 'Allyza',     'last_name' => '',              'username' => 'allyza',  'mobile_number' => '09011234567', 'role' => 'analyst'],
            ['first_name' => 'Kevin',      'last_name' => 'Lim',           'username' => 'kev',     'mobile_number' => '09171234567', 'role' => 'manager'],
            ['first_name' => 'Milo',       'last_name' => 'Gorospe',       'username' => 'milo',    'mobile_number' => '09181234567', 'role' => 'researcher'],
            ['first_name' => 'Well',       'last_name' => 'Dacoco',        'username' => 'well',    'mobile_number' => '09221234567', 'role' => 'researcher'],
            ['first_name' => 'Jamie',      'last_name' => 'Ortiz',         'username' => 'jamie',   'mobile_number' => '09211234567', 'role' => 'researcher'],
            ['first_name' => 'Czein',      'last_name' => 'Laruscain',     'username' => 'czein',   'mobile_number' => '09201234567', 'role' => 'content'],
            ['first_name' => 'Em',         'last_name' => 'Delos Santos',  'username' => 'em',      'mobile_number' => '09231234567', 'role' => 'content'],
            ['first_name' => 'Mark Ivan',  'last_name' => 'Empleo',        'username' => 'ivan',    'mobile_number' => '09241234567', 'role' => 'content'],
            ['first_name' => 'Tammy',      'last_name' => 'Flores',        'username' => 'tami',    'mobile_number' => '09251234567', 'role' => 'content'],
            ['first_name' => 'Fern',       'last_name' => '',              'username' => 'fern',    'mobile_number' => '09261234567', 'role' => 'graphics'],
            ['first_name' => 'Angelo',     'last_name' => '',              'username' => 'angelo',  'mobile_number' => '09281234567', 'role' => 'graphics'],
            ['first_name' => 'Latrell',    'last_name' => '',              'username' => 'latrell', 'mobile_number' => '09291234567', 'role' => 'graphics'],
            ['first_name' => 'Tim',        'last_name' => '',              'username' => 'tim',     'mobile_number' => '09271234567', 'role' => 'graphics'],
            ['first_name' => 'Angelyn',    'last_name' => 'Catolico',      'username' => 'ange',    'mobile_number' => '09191234567', 'role' => 'backend'],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'first_name'    => $data['first_name'],
                    'last_name'     => $data['last_name'],
                    'mobile_number' => $data['mobile_number'],
                    'password'      => Hash::make('1234'),
                    'role'          => $data['role'],
                ]
            );
        }
    }
}
