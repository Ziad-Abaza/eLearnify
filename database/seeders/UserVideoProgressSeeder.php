<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserVideoProgress;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Str;

class UserVideoProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserVideoProgress::truncate();

        $student1 = User::where('email', 'student1@example.com')->first();
        $laravelIntroVideo = Video::where('title', 'Introduction to Laravel')->first();
        $laravelRoutingVideo = Video::where('title', 'Laravel Routing')->first();

        if ($student1 && $laravelIntroVideo) {
            UserVideoProgress::create([
                'progress_id' => Str::uuid()->toString(),
                'user_id' => $student1->user_id,
                'video_id' => $laravelIntroVideo->video_id,
                'is_completed' => true,
                'last_watched_time' => now()->subHours(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($student1 && $laravelRoutingVideo) {
            UserVideoProgress::create([
                'progress_id' => Str::uuid()->toString(),
                'user_id' => $student1->user_id,
                'video_id' => $laravelRoutingVideo->video_id,
                'is_completed' => false,
                'last_watched_time' => now()->subHours(2),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Add more video progress records as needed
    }
}
