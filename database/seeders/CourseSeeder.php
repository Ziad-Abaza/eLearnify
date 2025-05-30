<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        // Truncate tables that depend on courses
        DB::table('videos')->truncate();
        DB::table('user_course_progress')->truncate();
        DB::table('certificates')->truncate();
        Course::truncate();
        Schema::enableForeignKeyConstraints();

        $webDevCategory = Category::where('category_name', 'Web Development')->first();
        $mobileDevCategory = Category::where('category_name', 'Mobile Development')->first();
        $instructor = User::where('email', 'instructor@example.com')->first();

        if (!$webDevCategory || !$mobileDevCategory || !$instructor) {
            $this->command->error('Required categories or instructor not found. Please run CategorySeeder and UserSeeder first.');
            return;
        }

        $courses = [
            [
                'course_id' => Str::uuid()->toString(),
                'title' => 'Laravel for Beginners',
                'description' => 'A comprehensive guide to Laravel framework for building modern web applications.',
                'instructor_id' => $instructor->user_id,
                'category_id' => $webDevCategory->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => Str::uuid()->toString(),
                'title' => 'Vue.js Fundamentals',
                'description' => 'Learn the fundamentals of Vue.js, the progressive JavaScript framework.',
                'instructor_id' => $instructor->user_id,
                'category_id' => $webDevCategory->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => Str::uuid()->toString(),
                'title' => 'React Native: Build Mobile Apps',
                'description' => 'Develop cross-platform mobile applications using React Native.',
                'instructor_id' => $instructor->user_id,
                'category_id' => $mobileDevCategory->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Course::insert($courses);
    }
}
