<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserCourseProgress;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Str;

class UserCourseProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserCourseProgress::truncate();

        $student1 = User::where('email', 'student1@example.com')->first();
        $laravelCourse = Course::where('title', 'Laravel for Beginners')->first();
        $vueCourse = Course::where('title', 'Vue.js Fundamentals')->first();

        if ($student1 && $laravelCourse) {
            UserCourseProgress::create([
                'user_course_id' => Str::uuid()->toString(),
                'user_id' => $student1->user_id,
                'course_id' => $laravelCourse->course_id,
                'completion_percentage' => 75.5,
                'last_accessed' => now()->subDays(2),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($student1 && $vueCourse) {
            UserCourseProgress::create([
                'user_course_id' => Str::uuid()->toString(),
                'user_id' => $student1->user_id,
                'course_id' => $vueCourse->course_id,
                'completion_percentage' => 30.0,
                'last_accessed' => now()->subDays(1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Add more progress records as needed
    }
}
