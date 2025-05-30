<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuestionOptionController extends Controller
{
    private function authorizeInstructorForQuestion(Question $question): bool
    {
        /** @var User $instructor */
        $instructor = Auth::user();
        // Question -> Video -> Course -> Instructor
        return $question->video && $question->video->course && $question->video->course->instructor_id === $instructor->user_id;
    }

    /**
     * Display a listing of options for a specific question.
     */
    public function index(Question $question)
    {
        if (!$this->authorizeInstructorForQuestion($question)) {
            return response()->json(['message' => 'Unauthorized. You cannot manage options for this question.'], 403);
        }

        $options = $question->options()->orderBy('question_option_id')->paginate(15); // Simple order for now
        return response()->json($options);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created option for a question.
     */
    public function store(Request $request, Question $question)
    {
        if (!$this->authorizeInstructorForQuestion($question)) {
            return response()->json(['message' => 'Unauthorized. You cannot add options to this question.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'option_text' => 'required|string|max:255',
            'is_correct' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        $isCorrect = $validatedData['is_correct'] ?? false;

        DB::beginTransaction();
        try {
            // If this option is marked as correct and the question is single-answer type,
            // ensure other options for this question are not marked as correct.
            if ($isCorrect && in_array($question->question_type, ['multiple_choice_single_answer', 'true_false'])) {
                $question->options()->update(['is_correct' => false]);
            }

            $option = $question->options()->create([
                'option_text' => $validatedData['option_text'],
                'is_correct' => $isCorrect,
                'course_id' => $question->course_id, // Set course_id from question
                'video_id' => $question->video_id,   // Set video_id from question
            ]);

            DB::commit();
            return response()->json($option, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create option.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified option.
     */
    public function show(QuestionOption $option)
    {
        if (!$this->authorizeInstructorForQuestion($option->question)) {
            return response()->json(['message' => 'Unauthorized. You cannot view this option.'], 403);
        }
        return response()->json($option->load('question:question_id,question_text'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionOption $questionOption)
    {
        //
    }

    /**
     * Update the specified option in storage.
     */
    public function update(Request $request, QuestionOption $option)
    {
        if (!$this->authorizeInstructorForQuestion($option->question)) {
            return response()->json(['message' => 'Unauthorized. You cannot update this option.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'option_text' => 'sometimes|required|string|max:255',
            'is_correct' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            if (array_key_exists('is_correct', $validatedData)) {
                $isCorrect = $validatedData['is_correct'];
                if ($isCorrect && in_array($option->question->question_type, ['multiple_choice_single_answer', 'true_false'])) {
                    $option->question->options()->where('question_option_id', '!=', $option->question_option_id)->update(['is_correct' => false]);
                }
                 $option->is_correct = $isCorrect; // Update the current option's is_correct status
            }

            if (array_key_exists('option_text', $validatedData)) {
                $option->option_text = $validatedData['option_text'];
            }
            $option->save();

            DB::commit();
            return response()->json($option);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update option.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified option from storage.
     */
    public function destroy(QuestionOption $option)
    {
        if (!$this->authorizeInstructorForQuestion($option->question)) {
            return response()->json(['message' => 'Unauthorized. You cannot delete this option.'], 403);
        }

        $option->delete();
        return response()->json(null, 204);
    }
}
