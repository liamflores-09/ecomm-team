<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->default('male')->after('mobile_number');
        });

        // Migrate the previously hardcoded female seeds to the new column
        DB::table('users')
            ->whereIn('username', ['jamie', 'em', 'ange', 'czein', 'well'])
            ->update(['gender' => 'female']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
