<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        if (!User::where('username', 'awin')->exists()) {
            User::create([
                'first_name'    => 'Mulawin',
                'last_name'     => 'Galang',
                'username'      => 'awin',
                'mobile_number' => '09001234567',
                'password'      => Hash::make('1234'),
                'role'          => 'head',
            ]);
        }
    }

    public function down(): void
    {
        User::where('username', 'awin')->delete();
    }
};
