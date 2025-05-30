<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Video;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        // Truncate tables that depend on questions
        DB::table('question_options')->truncate();
        DB::table('quiz_attempts')->truncate(); // quiz_attempts links to questions and options
        Question::truncate();
        Schema::enableForeignKeyConstraints();

        $laravelRoutingVideo = Video::where('title', 'Laravel Routing')->first();
        $vueComponentsVideo = Video::where('title', 'Vue Components')->first();

        if (!$laravelRoutingVideo || !$vueComponentsVideo) {
            $this->command->error('Required videos not found. Please run VideoSeeder first.');
            return;
        }

        $questions = [
            // Questions for Laravel Routing Video
            [
                'question_id' => Str::uuid()->toString(),
                'video_id' => $laravelRoutingVideo->video_id,
                'question_text' => 'What file primarily defines web routes in Laravel?',
                'question_type' => 'single_choice',
                'points' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question_id' => Str::uuid()->toString(),
                'video_id' => $laravelRoutingVideo->video_id,
                'question_text' => 'Which Artisan command can list all registered routes?',
                'question_type' => 'single_choice',
                'points' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Questions for Vue Components Video
            [
                'question_id' => Str::uuid()->toString(),
                'video_id' => $vueComponentsVideo->video_id,
                'question_text' => 'What is the typical file extension for a single-file Vue component?',
                'question_type' => 'single_choice',
                'points' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question_id' => Str::uuid()->toString(),
                'video_id' => $vueComponentsVideo->video_id,
                'question_text' => 'Props are used to pass data from parent to child components. True or False?',
                'question_type' => 'true_false',
                'points' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Question::insert($questions);
    }
}
