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
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->renameColumn('new_sku', 'task_1');
            $table->renameColumn('variation_sku', 'task_2');
            $table->renameColumn('advance_data_gathering', 'task_3');
            $table->renameColumn('update_listings', 'task_4');
            $table->renameColumn('other_tasks', 'task_5');
        });
    }

    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->renameColumn('task_1', 'new_sku');
            $table->renameColumn('task_2', 'variation_sku');
            $table->renameColumn('task_3', 'advance_data_gathering');
            $table->renameColumn('task_4', 'update_listings');
            $table->renameColumn('task_5', 'other_tasks');
        });
    }
};
