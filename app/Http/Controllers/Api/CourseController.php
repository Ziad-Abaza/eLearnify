<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Requests\Courses\StoreCourseRequest;
use App\Http\Requests\Courses\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Throwable;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $courses = Course::with(['category', 'instructor'])->paginate($perPage);

            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => CourseResource::collection($courses->items()),
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                    'has_more_pages' => $courses->hasMorePages(),
                ]
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve courses',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $data = $request->validated();

            $course = Course::create($data);

            if ($request->hasFile('image')) {
                $course->setImage($request->file('image'));
            }

            if ($request->hasFile('icon')) {
                $course->setIcon($request->file('icon'));
            }

            return response()->json([
                'success' => true,
                'code' => 201,
                'message' => 'Course created successfully',
                'data' => new \App\Http\Resources\CourseResource($course->refresh()),
            ], Response::HTTP_CREATED);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to create course',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Course $course)
    {
        try {
            $course->load(['category', 'instructor', 'videos']);
            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => new \App\Http\Resources\CourseResource($course),
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve course',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        try {
            $data = $request->safe()->only(['title', 'description', 'instructor_id', 'category_id']);
            $course->update($data);

            if ($request->hasFile('image')) {
                $course->setImage($request->file('image'));
            }

            if ($request->hasFile('icon')) {
                $course->setIcon($request->file('icon'));
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Course updated successfully',
                'data' => new \App\Http\Resources\CourseResource($course),
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to update course',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Course $course)
    {
        try {
            $course->deleteImage();
            $course->deleteIcon();
            $course->delete();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Course deleted successfully',
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to delete course',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
