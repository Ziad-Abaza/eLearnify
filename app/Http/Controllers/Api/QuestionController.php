<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Requests\Quiz\StoreQuestionRequest;
use App\Http\Requests\Quiz\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use Illuminate\Http\Response;
use Throwable;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $query = Question::with(['video']);

            if ($request->filled('video_id')) {
                $query->where('video_id', $request->video_id);
            }

            $questions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'data' => QuestionResource::collection($questions->items()),
                'pagination' => [
                    'current_page' => $questions->currentPage(),
                    'last_page' => $questions->lastPage(),
                    'per_page' => $questions->perPage(),
                    'total' => $questions->total(),
                    'has_more_pages' => $questions->hasMorePages(),
                ],
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve questions',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreQuestionRequest $request)
    {
        try {
            $question = Question::create($request->validated());
            $question->load('video');

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Question created successfully',
                'data' => new QuestionResource($question),
            ], Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to create question',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Question $question)
    {
        try {
            $question->load(['video', 'questionOptions']);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'data' => new QuestionResource($question),
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve question',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        try {
            $question->update($request->validated());
            $question->load('video');

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Question updated successfully',
                'data' => new QuestionResource($question),
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to update question',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Question $question)
    {
        try {
            $question->delete();

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_NO_CONTENT,
                'message' => 'Question deleted successfully',
            ], Response::HTTP_NO_CONTENT);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete question',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
