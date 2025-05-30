<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserVideoProgressRequest;
use App\Http\Requests\UpdateUserVideoProgressRequest;
use App\Http\Resources\UserVideoProgressResource;
use App\Models\UserVideoProgress;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserVideoProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = UserVideoProgress::with(['user', 'video']);

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->filled('video_id')) {
                $query->where('video_id', $request->video_id);
            }

            $data = $query->paginate(10);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'User video progress fetched successfully',
                'data' => UserVideoProgressResource::collection($data),
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to fetch user video progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserVideoProgressRequest $request)
    {
        try {
            $existing = UserVideoProgress::where('user_id', $request->user_id)
                ->where('video_id', $request->video_id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'code' => Response::HTTP_CONFLICT,
                    'message' => 'User video progress for this video already exists. Use PUT to update.',
                ], Response::HTTP_CONFLICT);
            }

            $progress = UserVideoProgress::create($request->validated());
            $progress->load(['user', 'video']);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'User video progress created successfully',
                'data' => new UserVideoProgressResource($progress),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to create user video progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserVideoProgress $userVideoProgress)
    {
        try {
            $userVideoProgress->load(['user', 'video']);
            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'User video progress retrieved successfully',
                'data' => new UserVideoProgressResource($userVideoProgress),
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve user video progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserVideoProgressRequest $request, UserVideoProgress $userVideoProgress)
    {
        try {
            $userVideoProgress->update($request->validated());
            $userVideoProgress->load(['user', 'video']);

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'User video progress updated successfully',
                'data' => new UserVideoProgressResource($userVideoProgress),
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to update user video progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserVideoProgress $userVideoProgress)
    {
        try {
            $userVideoProgress->delete();

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_NO_CONTENT,
                'message' => 'User video progress deleted successfully',
            ], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete user video progress',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
