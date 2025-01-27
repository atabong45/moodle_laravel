<?php

namespace Database\Seeders;

use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AssignmentsTableSeeder extends Seeder
{
    public function run()
    {
        // Création de 3 évaluations
        $assignments = [
            [
                'name' => 'Évaluation 1',
                'duedate' => Carbon::now()->addDays(10),
                'attemptnumber' => 3,
                'module_id' => 1,
            ],
            [
                'name' => 'Évaluation 2',
                'duedate' => Carbon::now()->addDays(15),
                'attemptnumber' => 2,
                'module_id' => 2,
            ],
            [
                'name' => 'Évaluation 3',
                'duedate' => Carbon::now()->addDays(20),
                'attemptnumber' => 1,
                'module_id' => 3,
            ],
        ];

        foreach ($assignments as $data) {
            Assignment::create($data);
        }

        $this->command->info('3 évaluations ont été ajoutées avec succès.');
    }
}
