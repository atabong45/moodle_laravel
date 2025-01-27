<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Créer un utilisateur étudiant
        $student = User::firstOrCreate(
            [
                'email' => 'student@example.com',
            ],
            [
                'name' => 'Étudiant Example',
                'password' => Hash::make('student12345'), // Mot de passe sécurisé
            ]
        );

        // Créer un utilisateur enseignant
        $teacher = User::firstOrCreate(
            [
                'email' => 'teacher@example.com',
            ],
            [
                'name' => 'Enseignant Example',
                'password' => Hash::make('teacher12345'), // Mot de passe sécurisé
            ]
        );

        // Assigner les rôles à l'étudiant
        $studentRole = 'ROLE_STUDENT';
        $student->assignRole($studentRole);

        // Assigner les rôles à l'enseignant
        $teacherRole = 'ROLE_TEACHER';
        $teacher->assignRole($teacherRole);

        $this->command->info('Utilisateur étudiant et enseignant créés avec succès.');
    }
}
