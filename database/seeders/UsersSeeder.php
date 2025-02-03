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
                // Créer un utilisateur enseignant
        $teacher1 = User::firstOrCreate(
            [
                'email' => 'teacher1@example.com',
            ],
            [
                'name' => 'Enseignant 1',
                'password' => Hash::make('teacher12345'), // Mot de passe sécurisé
            ]
        );

                // Créer un utilisateur enseignant
        $teacher2 = User::firstOrCreate(
            [
                'email' => 'teacher2@example.com',
            ],
            [
                'name' => 'Enseignant 2',
                'password' => Hash::make('teacher12345'), // Mot de passe sécurisé
            ]
        );

        // Créer un utilisateur étudiant
        $student1 = User::firstOrCreate(
            [
                'email' => 'student@example.com',
            ],
            [
                'name' => 'Étudiant 1',
                'password' => Hash::make('student12345'), // Mot de passe sécurisé
            ]
        );
        $student2 = User::firstOrCreate(
            [
                'email' => 'student2@example.com',
            ],
            [
                'name' => 'Étudiant 2',
                'password' => Hash::make('student12345'), // Mot de passe sécurisé
            ]
        );
        $student3 = User::firstOrCreate(
            [
                'email' => 'student3@example.com',
            ],
            [
                'name' => 'Étudiant 3',
                'password' => Hash::make('student12345'), // Mot de passe sécurisé
            ]
        );


        // Assigner les rôles à l'étudiant
        $studentRole = 'ROLE_STUDENT';

        $student1->assignRole($studentRole);
        $student2->assignRole($studentRole);
        $student3->assignRole($studentRole);

        // Assigner les rôles à l'enseignant
        $teacherRole = 'ROLE_TEACHER';
        $teacher1->assignRole($teacherRole);
        $teacher1->assignRole($teacherRole);

        $this->command->info('Utilisateur étudiant et enseignant créés avec succès.');
    }
}
