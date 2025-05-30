<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Ensure User model is imported
use App\Models\QuizAttempt; // For storing quiz results
use App\Models\QuestionOption; // To get correct answers
use Illuminate\Support\Facades\Validator; // For input validation
use Illuminate\Support\Facades\DB; // For transaction

class QuizController extends Controller
{
    /**
     * Display the quiz (questions and options) for a specific video.
     * Options will not include the is_correct flag.
     */
    public function showQuizForVideo(Request $request, Video $video)
    {
        /** @var User $user */
        $user = Auth::user();

        // First, check if the user is enrolled in the course this video belongs to.
        if ($video->course) {
            $isEnrolled = $user->userCourseProgress()->where('course_id', $video->course->course_id)->exists();
            if (!$isEnrolled) {
                return response()->json(['message' => 'You must be enrolled in the course to take this quiz.'], 403);
            }
        }
        // If video has no course, or course check passes, proceed.

        $questions = Question::where('video_id', $video->video_id)
            ->with(['options' => function ($query) {
                // Select only necessary fields, explicitly excluding is_correct
                $query->select('question_option_id', 'question_id', 'option_text');
            }])
            ->orderBy('order_in_quiz', 'asc') // Assuming you have an order column
            ->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'No quiz found for this video.'], 404);
        }

        return response()->json([
            'video_id' => $video->video_id,
            'video_title' => $video->title,
            'quiz_questions' => $questions
        ]);
    }

    /**
     * Submit answers for a quiz and record the attempt.
     */
    public function submitQuizAnswers(Request $request, Video $video)
    {
        /** @var User $user */
        $user = Auth::user();

        // Validate input: expecting an array of answers
        // Each answer: { "question_id": "uuid", "selected_option_id": "uuid" }
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|uuid|exists:questions,question_id',
            'answers.*.selected_option_id' => 'required|uuid|exists:question_options,question_option_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check enrollment
        if ($video->course) {
            $isEnrolled = $user->userCourseProgress()->where('course_id', $video->course->course_id)->exists();
            if (!$isEnrolled) {
                return response()->json(['message' => 'You must be enrolled in the course to submit this quiz.'], 403);
            }
        }

        $submittedAnswers = $request->input('answers');
        $score = 0;
        $totalQuestionsInQuiz = 0;

        DB::beginTransaction();
        try {
            // Fetch all questions for this video's quiz to ensure we process all of them
            // and to get their correct options.
            $quizQuestions = Question::where('video_id', $video->video_id)
                                   ->with('options') // Load all options to find the correct one
                                   ->get();

            $totalQuestionsInQuiz = $quizQuestions->count();
            if ($totalQuestionsInQuiz === 0) {
                return response()->json(['message' => 'No questions found for this quiz. Cannot submit.'], 404);
            }

            $attemptDetails = []; // To store individual answer results for the QuizAttempt

            foreach ($quizQuestions as $question) {
                $userAnswerForQuestion = collect($submittedAnswers)->firstWhere('question_id', $question->question_id);
                $correctOption = $question->options->firstWhere('is_correct', true);

                $isCorrect = false;
                $selectedOptionId = null;

                if ($userAnswerForQuestion && $correctOption) {
                    $selectedOptionId = $userAnswerForQuestion['selected_option_id'];
                    if ($selectedOptionId === $correctOption->question_option_id) {
                        $score++;
                        $isCorrect = true;
                    }
                }
                // Store details for each question attempt (even if not answered or no correct option defined for some reason)
                $attemptDetails[] = [
                    'question_id' => $question->question_id,
                    'selected_option_id' => $selectedOptionId,
                    'correct_option_id' => $correctOption ? $correctOption->question_option_id : null,
                    'is_correct' => $isCorrect,
                ];
            }

            $percentageScore = ($totalQuestionsInQuiz > 0) ? ($score / $totalQuestionsInQuiz) * 100 : 0;

            // Create QuizAttempt record
            $quizAttempt = QuizAttempt::create([
                // 'quiz_attempt_id' => Uuid::uuid4()->toString(), // Handled by HasUuids trait
                'user_id' => $user->user_id,
                'video_id' => $video->video_id,
                'course_id' => $video->course_id, // Assuming video is always linked to a course
                'score' => $score,
                'total_questions' => $totalQuestionsInQuiz,
                'percentage' => $percentageScore,
                'attempt_details' => $attemptDetails, // Storing as JSON
                'attempted_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Quiz submitted successfully.',
                'attempt' => $quizAttempt
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to submit quiz.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * List quiz attempts for the authenticated user, optionally filtered.
     */
    public function listUserAttempts(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = QuizAttempt::where('user_id', $user->user_id);

        if ($request->has('video_id')) {
            $query->where('video_id', $request->input('video_id'));
        }

        if ($request->has('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        $attempts = $query->with(['video:video_id,title', 'course:course_id,title'])
                           ->orderBy('attempted_at', 'desc')
                           ->paginate(15);

        return response()->json($attempts);
    }

    /**
     * Show a specific quiz attempt for the authenticated user.
     */
    public function showUserAttempt(Request $request, QuizAttempt $quizAttempt)
    {
        /** @var User $user */
        $user = Auth::user();

        // Ensure the attempt belongs to the authenticated user
        if ($quizAttempt->user_id !== $user->user_id) {
            return response()->json(['message' => 'Unauthorized to view this quiz attempt.'], 403);
        }

        // Eager load related data if needed, e.g., video title, course title
        $quizAttempt->load(['video:video_id,title', 'course:course_id,title']);

        // The attempt_details field already contains the questions, selected answers, and correct answers.
        // If you need to load Question models and Option models, you'd do more complex queries here based on attempt_details.

        return response()->json($quizAttempt);
    }
}
