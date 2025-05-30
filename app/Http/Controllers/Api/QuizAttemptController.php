<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quiz\StoreQuizAttemptRequest;
use App\Http\Requests\Quiz\UpdateQuizAttemptRequest;
use App\Http\Resources\QuizAttemptResource;
use App\Models\QuizAttempt;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QuizAttemptController extends Controller
{
    public function index(Request $request)
    {
        $query = QuizAttempt::with(['user', 'question', 'selectedOption']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('question_id')) {
            $query->where('question_id', $request->question_id);
        }

        $quizAttempts = $query->latest('attempt_time')->get();
        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => QuizAttemptResource::collection($quizAttempts),
        ], Response::HTTP_OK);
    }

    public function store(StoreQuizAttemptRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['is_correct'])) {
            $selectedOption = QuestionOption::find($data['selected_option_id']);
            if ($selectedOption) {
                $data['is_correct'] = $selectedOption->is_correct;
            }
        }

        $quizAttempt = QuizAttempt::create($data);
        $quizAttempt->load(['user', 'question', 'selectedOption']);
        return response()-> json([
            'success' => true,
            'code' => 201,
            'data' => new QuizAttemptResource($quizAttempt),
        ], Response::HTTP_CREATED);
    }

    public function show(QuizAttempt $quizAttempt)
    {
        $quizAttempt->load(['user', 'question', 'selectedOption']);
        return response()-> json([
            'success' => true,
            'code' => 200,
            'data' => new QuizAttemptResource($quizAttempt),
        ], Response::HTTP_OK);
    }

    public function update(UpdateQuizAttemptRequest $request, QuizAttempt $quizAttempt)
    {
        $data = $request->validated();

        if ($request->has('selected_option_id') && !isset($data['is_correct'])) {
            $selectedOption = QuestionOption::find($data['selected_option_id']);
            if ($selectedOption) {
                $data['is_correct'] = $selectedOption->is_correct;
            }
        } elseif (!isset($data['is_correct'])) {
            unset($data['is_correct']);
        }

        $quizAttempt->update($data);
        $quizAttempt->load(['user', 'question', 'selectedOption']);
        return response()-> json([
            'success' => true,
            'code' => 200,
            'data' => new QuizAttemptResource($quizAttempt),
        ], Response::HTTP_OK);
    }

    public function destroy(QuizAttempt $quizAttempt)
    {
        $quizAttempt->delete();
        return response()-> json([
            'success' => true,
            'code' => 200,
            'message' => 'Quiz attempt deleted successfully',
        ], Response::HTTP_OK);
    }
}
