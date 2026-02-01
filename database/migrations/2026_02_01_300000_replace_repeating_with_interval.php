<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planned_tasks', function (Blueprint $table) {
            $table->json('interval')->nullable()->default(null);
        });

        // Migrate existing repeating tasks
        $tasks = DB::table('planned_tasks')->where('repeating', true)->get();
        foreach ($tasks as $task) {
            $time = date('H:i', strtotime($task->execute_at));
            DB::table('planned_tasks')->where('id', $task->id)->update([
                'interval' => json_encode(['type' => 'at_times_of_day', 'times' => [$time]]),
            ]);
        }

        Schema::table('planned_tasks', function (Blueprint $table) {
            $table->dropColumn('repeating');
        });
    }

    public function down(): void
    {
        Schema::table('planned_tasks', function (Blueprint $table) {
            $table->boolean('repeating')->default(false);
        });

        DB::table('planned_tasks')->whereNotNull('interval')->update(['repeating' => true]);

        Schema::table('planned_tasks', function (Blueprint $table) {
            $table->dropColumn('interval');
        });
    }
};
