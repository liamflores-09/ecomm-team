<?php

namespace Database\Seeders;

use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class DailyLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('username', ['czein', 'em', 'ivan', 'tami'])->pluck('id', 'username')->toArray();

        $data = [
            // W23 — June 8
            ['username' => 'czein', 'date' => '2026-06-08', 'task_1' => 13, 'task_2' => 9, 'task_3' => 25, 'task_4' => 0,  'task_5' => 295],
            ['username' => 'em',    'date' => '2026-06-08', 'task_1' => 18, 'task_2' => 4, 'task_3' => 17, 'task_4' => 25, 'task_5' => 140],
            ['username' => 'ivan',  'date' => '2026-06-08', 'task_1' => 7,  'task_2' => 7, 'task_3' => 16, 'task_4' => 45, 'task_5' => 1117],
            ['username' => 'tami',  'date' => '2026-06-08', 'task_1' => 21, 'task_2' => 8, 'task_3' => 38, 'task_4' => 22, 'task_5' => 36],

            // W24 — June 15
            ['username' => 'czein', 'date' => '2026-06-15', 'task_1' => 8,  'task_2' => 6, 'task_3' => 14, 'task_4' => 0,   'task_5' => 23],
            ['username' => 'em',    'date' => '2026-06-15', 'task_1' => 7,  'task_2' => 3, 'task_3' => 7,  'task_4' => 14,  'task_5' => 179],
            ['username' => 'ivan',  'date' => '2026-06-15', 'task_1' => 5,  'task_2' => 9, 'task_3' => 10, 'task_4' => 168, 'task_5' => 746],
            ['username' => 'tami',  'date' => '2026-06-15', 'task_1' => 7,  'task_2' => 7, 'task_3' => 8,  'task_4' => 3,   'task_5' => 196],

            // W25 — June 22
            ['username' => 'czein', 'date' => '2026-06-22', 'task_1' => 13, 'task_2' => 7,  'task_3' => 16, 'task_4' => 0,    'task_5' => 36],
            ['username' => 'em',    'date' => '2026-06-22', 'task_1' => 2,  'task_2' => 4,  'task_3' => 15, 'task_4' => 39,   'task_5' => 368],
            ['username' => 'ivan',  'date' => '2026-06-22', 'task_1' => 8,  'task_2' => 8,  'task_3' => 16, 'task_4' => 1593, 'task_5' => 4426],
            ['username' => 'tami',  'date' => '2026-06-22', 'task_1' => 8,  'task_2' => 3,  'task_3' => 26, 'task_4' => 34,   'task_5' => 405],
        ];

        foreach ($data as $row) {
            DailyLog::updateOrCreate(
                ['user_id' => $users[$row['username']], 'date' => $row['date']],
                [
                    'task_1' => $row['task_1'],
                    'task_2' => $row['task_2'],
                    'task_3' => $row['task_3'],
                    'task_4' => $row['task_4'],
                    'task_5' => $row['task_5'],
                ]
            );
        }
    }
}
