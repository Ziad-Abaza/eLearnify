<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserCourseProgressRequest;
use App\Http\Requests\UpdateUserCourseProgressRequest;
use App\Http\Resources\UserCourseProgressResource;
use App\Models\UserCourseProgress;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserCourseProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $query = UserCourseProgress::with(['user', 'course']);

            if (request()->filled('user_id')) {
                $query->where('user_id', request()->user_id);
            }

            if (request()->filled('course_id')) {
                $query->where('course_id', request()->course_id);
            }

            $data = $query->paginate(10);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'User course progress fetched successfully',
                'data' => UserCourseProgressResource::collection($data)
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to fetch user course progress: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to fetch user course progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserCourseProgressRequest $request)
    {
        try {
            // Prevent duplicate entries
            $exists = UserCourseProgress::where('user_id', $request->user_id)
                ->where('course_id', $request->course_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'code' => Response::HTTP_CONFLICT,
                    'message' => 'User course progress for this course already exists. Use PUT to update.',
                ], Response::HTTP_CONFLICT);
            }

            $progress = UserCourseProgress::create($request->validated());
            $progress->load(['user', 'course']);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'User course progress created successfully',
                'data' => new UserCourseProgressResource($progress)
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Failed to create user course progress: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to create user course progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserCourseProgress $userCourseProgress)
    {
        try {
            $userCourseProgress->load(['user', 'course']);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'User course progress fetched successfully',
                'data' => new UserCourseProgressResource($userCourseProgress)
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to fetch user course progress: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to fetch user course progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserCourseProgressRequest $request, UserCourseProgress $userCourseProgress)
    {
        try {
            $userCourseProgress->update($request->validated());
            $userCourseProgress->load(['user', 'course']);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'User course progress updated successfully',
                'data' => new UserCourseProgressResource($userCourseProgress)
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to update user course progress: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to update user course progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserCourseProgress $userCourseProgress)
    {
        try {
            $userCourseProgress->delete();

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_NO_CONTENT,
                'message' => 'User course progress deleted successfully',
            ], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            Log::error('Failed to delete user course progress: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete user course progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
