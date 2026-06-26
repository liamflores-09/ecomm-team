<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        if (!User::where('username', 'allyza')->exists()) {
            User::create([
                'first_name'    => 'Allyza',
                'last_name'     => '',
                'username'      => 'allyza',
                'mobile_number' => '09011234567',
                'password'      => Hash::make('1234'),
                'role'          => 'analyst',
            ]);
        }
    }

    public function down(): void
    {
        User::where('username', 'allyza')->delete();
    }
};
