<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('attendance')->nullable();
            $table->unsignedInteger('new_sku')->default(0);
            $table->unsignedInteger('variation_sku')->default(0);
            $table->unsignedInteger('advance_data_gathering')->default(0);
            $table->unsignedInteger('update_listings')->default(0);
            $table->unsignedInteger('other_tasks')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
