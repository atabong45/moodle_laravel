<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssignmentsTableSeeder extends Seeder
{
    public function run()
    {
        // Récupérer tous les modules existants
        $modules = Module::all();

        // Assurez-vous qu'il y a des modules dans la base de données avant de procéder
        if ($modules->isEmpty()) {
            $this->command->error('Aucun module trouvé dans la base de données!');
            return;
        }

        // Créer 5 assignments pour chaque module
        foreach ($modules as $module) {
            // Créer 5 assignments pour chaque module
            for ($i = 1; $i <= 5; $i++) {
                Assignment::create([
                    'name' => "Assignment $i - " . $module->name,
                    'duedate' => Carbon::now()->addDays(rand(1, 30)), // Date d'échéance aléatoire entre 1 et 30 jours
                    'attemptnumber' => 1, // Pour l'instant, nous définissons un seul essai
                    'module_id' => $module->id, // Assignation du module
                ]);
            }
        }

        $this->command->info('Assignments ajoutés avec succès.');
    }
}
