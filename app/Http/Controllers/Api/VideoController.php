<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Http\Requests\Video\StoreVideoRequest;
use App\Http\Requests\Video\UpdateVideoRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class VideoController extends Controller
{

    public function index(Request $request)
    {
        try {
            // Start query with eager loading
            $query = Video::with('course');

            // Filter by course_id if provided
            if ($request->filled('course_id')) {
                    $query->where('course_id', $request->input('course_id'));
            }

            // Search by title
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where('title', 'like', "%{$searchTerm}%");
            }

            // Pagination: default 10 per page, allow override via request
            $perPage = $request->get('per_page', 10); // Default to 10 items per page
            $page = $request->get('page', 1);

            // Order by order_in_course
            $query->orderBy('order_in_course');

            // Execute pagination
            $videos = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform data using VideoResource
            $videos->getCollection()->transform(fn($v) => new \App\Http\Resources\VideoResource($v));

            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => $videos->items(),
                'pagination' => [
                    'current_page' => $videos->currentPage(),
                    'from' => $videos->firstItem(),
                    'to' => $videos->lastItem(),
                    'per_page' => $videos->perPage(),
                    'total' => $videos->total(),
                    'last_page' => $videos->lastPage(),
                    'has_more_pages' => $videos->hasMorePages(),
                    'next_page_url' => $videos->nextPageUrl(),
                    'prev_page_url' => $videos->previousPageUrl(),
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve videos',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function store(StoreVideoRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['order_in_course'] = $this->getNextOrderNumber($request->course_id);

            $video = Video::create($validated);

            if ($request->hasFile('thumbnail')) {
                $video->setThumbnail($request->file('thumbnail'));
            }

            if ($request->hasFile('video_file')) {
                $video->setVideoFile($request->file('video_file'));
            }

            return response()->json([
                'success' => true,
                'code' => 201,
                'message' => 'Video created successfully',
                'data' => new \App\Http\Resources\VideoResource($video->refresh()),
            ], Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to create video',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Video $video)
    {
        try {
            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => new \App\Http\Resources\VideoResource($video),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve video',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        try {
            $validated = $request->validated();

            $video->update($validated);

            if ($request->hasFile('thumbnail')) {
                $video->setThumbnail($request->file('thumbnail'));
            }

            if ($request->hasFile('video_file')) {
                $video->setVideoFile($request->file('video_file'));
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Video updated successfully',
                'data' => new \App\Http\Resources\VideoResource($video),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to update video',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Video $video)
    {
        try {
            $video->delete();
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Video deleted successfully'
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to delete video',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getNextOrderNumber(string $courseId): int
    {
        return Video::where('course_id', $courseId)->max('order_in_course') + 1;
    }
}
