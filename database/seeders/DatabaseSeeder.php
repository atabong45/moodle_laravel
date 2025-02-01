<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Appeler le seeder des rôles d'abord
        $this->call(RoleSeeder::class);

        // Ensuite, appeler le seeder de l'administrateur
        $this->call(AdminUserSeeder::class);

        $this->call(UsersSeeder::class);

        $this->call(CategoriesTableSeeder::class);

        $this->call(CoursesTableSeeder::class);

        $this->call(QuestionsTableSeeder::class);

        $this->call(AssignmentsTableSeeder::class);

        $this->call(EventSeeder::class);

        // $this->call(SubmissionsTableSeeder::class);


    }
}
