<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('badge')->nullable()->after('gender');
        });

        // Move Milo Gorospe from lead → researcher and mark his badge
        DB::table('users')
            ->where('first_name', 'Milo')
            ->where('last_name', 'Gorospe')
            ->update(['role' => 'researcher', 'badge' => 'Content/PR Lead']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('first_name', 'Milo')
            ->where('last_name', 'Gorospe')
            ->update(['role' => 'lead', 'badge' => null]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('badge');
        });
    }
};
