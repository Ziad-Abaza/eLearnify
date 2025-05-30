<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    private function authorizeInstructorForVideo(Video $video): bool
    {
        /** @var User $instructor */
        $instructor = Auth::user();
        // A video belongs to a course, and a course has an instructor.
        return $video->course && $video->course->instructor_id === $instructor->user_id;
    }

    /**
     * Display a listing of questions for a specific video.
     */
    public function index(Video $video)
    {
        if (!$this->authorizeInstructorForVideo($video)) {
            return response()->json(['message' => 'Unauthorized. You cannot manage questions for this video.'], 403);
        }

        $questions = $video->questions()->orderBy('order_in_quiz', 'asc')->with('options')->paginate(15);
        return response()->json($questions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created question for a video.
     */
    public function store(Request $request, Video $video)
    {
        if (!$this->authorizeInstructorForVideo($video)) {
            return response()->json(['message' => 'Unauthorized. You cannot add questions to this video.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice_single_answer,multiple_choice_multiple_answers,true_false,short_answer',
            'order_in_quiz' => 'nullable|integer|min:0',
            'feedback' => 'nullable|string',
            // 'options' can be handled separately or as part of a more complex request
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        $question = $video->questions()->create([
            'question_text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
            'order_in_quiz' => $validatedData['order_in_quiz'] ?? $video->questions()->max('order_in_quiz') + 1 ?? 1,
            'feedback' => $validatedData['feedback'] ?? null,
            'course_id' => $video->course_id, // Set course_id from video
        ]);

        // If options are provided, one might create them here too in a transaction

        return response()->json($question->load('options'), 201);
    }

    /**
     * Display the specified question.
     */
    public function show(Question $question)
    {
        if (!$this->authorizeInstructorForVideo($question->video)) {
            return response()->json(['message' => 'Unauthorized. You cannot view this question.'], 403);
        }
        return response()->json($question->load('video:video_id,title', 'options'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Question $question)
    {
        if (!$this->authorizeInstructorForVideo($question->video)) {
            return response()->json(['message' => 'Unauthorized. You cannot update this question.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'question_text' => 'sometimes|required|string',
            'question_type' => 'sometimes|required|in:multiple_choice_single_answer,multiple_choice_multiple_answers,true_false,short_answer',
            'order_in_quiz' => 'nullable|integer|min:0',
            'feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $question->update($validator->validated());
        return response()->json($question->load('options'));
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        if (!$this->authorizeInstructorForVideo($question->video)) {
            return response()->json(['message' => 'Unauthorized. You cannot delete this question.'], 403);
        }

        // Related question options will be deleted by cascade if foreign keys are set up correctly
        $question->delete();
        return response()->json(null, 204);
    }
}
