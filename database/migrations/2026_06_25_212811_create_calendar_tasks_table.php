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
        Schema::create('calendar_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('calendar_categories')->cascadeOnDelete();
            $table->string('title');
            $table->date('due_date');
            $table->string('assigned_role'); // content, graphics, backend, researcher, manager
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_tasks');
    }
};
