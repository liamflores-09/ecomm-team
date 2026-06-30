<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->after('last_name');
            $table->string('id_number')->nullable()->after('avatar');
            $table->string('tin')->nullable()->after('id_number');
            $table->string('sss')->nullable()->after('tin');
            $table->text('address')->nullable()->after('sss');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'id_number', 'tin', 'sss', 'address']);
        });
    }
};
