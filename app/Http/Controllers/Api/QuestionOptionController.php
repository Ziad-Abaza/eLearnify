<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use App\Http\Requests\Quiz\StoreQuestionOptionRequest;
use App\Http\Requests\Quiz\UpdateQuestionOptionRequest;
use App\Http\Resources\QuestionOptionResource;
use Illuminate\Http\Response;
use Throwable;

class QuestionOptionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $query = QuestionOption::with('question');

            if ($request->filled('question_id')) {
                $query->where('question_id', $request->question_id);
            }

            $options = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'data' => QuestionOptionResource::collection($options->items()),
                'pagination' => [
                    'current_page' => $options->currentPage(),
                    'last_page' => $options->lastPage(),
                    'per_page' => $options->perPage(),
                    'total' => $options->total(),
                    'has_more_pages' => $options->hasMorePages(),
                ],
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve question options',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreQuestionOptionRequest $request)
    {
        try {
            $question = \App\Models\Question::find($request->question_id);

            if ($question && in_array($question->question_type, ['single_choice', 'true_false']) && $request->is_correct) {
                // Set other options to not correct
                QuestionOption::where('question_id', $request->question_id)->update(['is_correct' => false]);
            }

            $option = QuestionOption::create($request->validated());
            $option->load('question');

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Question option created successfully',
                'data' => new QuestionOptionResource($option),
            ], Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to create question option',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(QuestionOption $questionOption)
    {
        try {
            $questionOption->load('question');

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'data' => new QuestionOptionResource($questionOption),
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve question option',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateQuestionOptionRequest $request, QuestionOption $questionOption)
    {
        try {
            $questionId = $request->question_id ?? $questionOption->question_id;
            $question = \App\Models\Question::find($questionId);

            if ($question && in_array($question->question_type, ['single_choice', 'true_false']) && $request->boolean('is_correct')) {
                QuestionOption::where('question_id', $question->question_id)
                    ->where('option_id', '!=', $questionOption->option_id)
                    ->update(['is_correct' => false]);
            }

            $questionOption->update($request->validated());
            $questionOption->load('question');

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Question option updated successfully',
                'data' => new QuestionOptionResource($questionOption),
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to update question option',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(QuestionOption $questionOption)
    {
        try {
            $questionOption->delete();

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_NO_CONTENT,
                'message' => 'Question option deleted successfully',
            ], Response::HTTP_NO_CONTENT);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete question option',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
