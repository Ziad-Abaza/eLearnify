<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Str;

class QuizAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuizAttempt::truncate();

        $student1 = User::where('email', 'student1@example.com')->first();
        $question1 = Question::where('question_text', 'What file primarily defines web routes in Laravel?')->first();

        if (!$student1 || !$question1) {
            $this->command->error('Required student or question not found for QuizAttemptSeeder. Please run UserSeeder and QuestionSeeder first.');
            return;
        }

        // Attempt for Question 1
        $optionForQ1 = QuestionOption::where('question_id', $question1->question_id)
                                     ->where('option_text', 'routes/web.php') // Assuming this is the correct option
                                     ->first();

        if ($optionForQ1) {
            QuizAttempt::create([
                'attempt_id' => Str::uuid()->toString(),
                'user_id' => $student1->user_id,
                'question_id' => $question1->question_id,
                'selected_option_id' => $optionForQ1->option_id,
                'attempt_time' => now(),
                'is_correct' => $optionForQ1->is_correct, // Set based on the option
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Add more quiz attempts as needed for other students and questions
        $student2 = User::where('email', 'student2@example.com')->first();
        $question2 = Question::where('question_text', 'Which Artisan command can list all registered routes?')->first();

        if($student2 && $question2){
            $optionForQ2 = QuestionOption::where('question_id', $question2->question_id)
                                        ->where('is_correct', true)
                                        ->first(); // Get the correct option
            if($optionForQ2){
                QuizAttempt::create([
                    'attempt_id' => Str::uuid()->toString(),
                    'user_id' => $student2->user_id,
                    'question_id' => $question2->question_id,
                    'selected_option_id' => $optionForQ2->option_id,
                    'attempt_time' => now(),
                    'is_correct' => $optionForQ2->is_correct,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
