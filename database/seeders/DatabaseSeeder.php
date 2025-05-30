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
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            VideoSeeder::class,
            QuestionSeeder::class,
            QuestionOptionSeeder::class,
            QuizAttemptSeeder::class, // Assuming this should run after users, questions, options
            CertificateSeeder::class, // Assuming this runs after users and courses
            UserCourseProgressSeeder::class, // After users and courses
            UserVideoProgressSeeder::class, // After users and videos
        ]);
    }
}
