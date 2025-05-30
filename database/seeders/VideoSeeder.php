<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Video;
use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        // Truncate tables that depend on videos
        DB::table('questions')->truncate();
        DB::table('user_video_progress')->truncate();
        Video::truncate();
        Schema::enableForeignKeyConstraints();

        $laravelCourse = Course::where('title', 'Laravel for Beginners')->first();
        $vueCourse = Course::where('title', 'Vue.js Fundamentals')->first();

        if (!$laravelCourse || !$vueCourse) {
            $this->command->error('Required courses not found. Please run CourseSeeder first.');
            return;
        }

        $videos = [
            // Laravel Course Videos
            [
                'video_id' => Str::uuid()->toString(),
                'course_id' => $laravelCourse->course_id,
                'title' => 'Introduction to Laravel',
                'duration' => 600, // 10 minutes
                's3_url' => 'https://example-s3.com/laravel_intro.mp4',
                'order_in_course' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'video_id' => Str::uuid()->toString(),
                'course_id' => $laravelCourse->course_id,
                'title' => 'Laravel Routing',
                'duration' => 900, // 15 minutes
                's3_url' => 'https://example-s3.com/laravel_routing.mp4',
                'order_in_course' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Vue.js Course Videos
            [
                'video_id' => Str::uuid()->toString(),
                'course_id' => $vueCourse->course_id,
                'title' => 'Getting Started with Vue',
                'duration' => 720, // 12 minutes
                's3_url' => 'https://example-s3.com/vue_getting_started.mp4',
                'order_in_course' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'video_id' => Str::uuid()->toString(),
                'course_id' => $vueCourse->course_id,
                'title' => 'Vue Components',
                'duration' => 1080, // 18 minutes
                's3_url' => 'https://example-s3.com/vue_components.mp4',
                'order_in_course' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Video::insert($videos);
    }
}
