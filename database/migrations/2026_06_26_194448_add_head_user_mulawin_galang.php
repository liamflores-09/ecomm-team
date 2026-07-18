<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        // Drop any leftover check constraint on role, whatever it's actually named
        $constraints = DB::select("
            SELECT conname FROM pg_constraint
            WHERE conrelid = 'users'::regclass
            AND contype = 'c'
            AND pg_get_constraintdef(oid) LIKE '%role%'
        ");

        foreach ($constraints as $constraint) {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS "' . $constraint->conname . '"');
        }

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
