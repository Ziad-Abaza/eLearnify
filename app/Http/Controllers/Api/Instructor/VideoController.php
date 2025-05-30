<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // Import User model
use Illuminate\Support\Str;

class VideoController extends Controller
{
    private function authorizeInstructorForCourse(Course $course): bool
    {
        /** @var User $instructor */
        $instructor = Auth::user();
        return $course->instructor_id === $instructor->user_id;
    }

    /**
     * Display a listing of videos for a specific course owned by the instructor.
     */
    public function index(Course $course)
    {
        if (!$this->authorizeInstructorForCourse($course)) {
            return response()->json(['message' => 'Unauthorized. You do not own this course.'], 403);
        }

        $videos = $course->videos()->orderBy('order_in_course', 'asc')->paginate(15);
        return response()->json($videos);
    }

    /**
     * Store a newly created video for a course owned by the instructor.
     */
    public function store(Request $request, Course $course)
    {
        if (!$this->authorizeInstructorForCourse($course)) {
            return response()->json(['message' => 'Unauthorized. You do not own this course.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'duration' => 'required|integer|min:1', // Duration in seconds
            'order_in_course' => 'nullable|integer|min:0',
            'is_preview_allowed' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        $video = $course->videos()->create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
            'video_url' => $validatedData['video_url'],
            'duration' => $validatedData['duration'],
            'order_in_course' => $validatedData['order_in_course'] ?? $course->videos()->max('order_in_course') + 1 ?? 1,
            'is_preview_allowed' => $validatedData['is_preview_allowed'] ?? false,
            'slug' => Str::slug($validatedData['title']) . '-' . Str::lower(Str::random(6)),
        ]);

        return response()->json($video, 201);
    }

    /**
     * Display the specified video if its parent course is owned by the instructor.
     */
    public function show(Video $video)
    {
        if (!$this->authorizeInstructorForCourse($video->course)) {
            return response()->json(['message' => 'Unauthorized. You do not own the course this video belongs to.'], 403);
        }
        return response()->json($video->load('course:course_id,title'));
    }

    /**
     * Update the specified video if its parent course is owned by the instructor.
     */
    public function update(Request $request, Video $video)
    {
        if (!$this->authorizeInstructorForCourse($video->course)) {
            return response()->json(['message' => 'Unauthorized. You do not own the course this video belongs to.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'sometimes|required|url',
            'duration' => 'sometimes|required|integer|min:1',
            'order_in_course' => 'nullable|integer|min:0',
            'is_preview_allowed' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        $updateData = $validatedData;

        if (isset($validatedData['title']) && $validatedData['title'] !== $video->title) {
            $updateData['slug'] = Str::slug($validatedData['title']) . '-' . Str::lower(Str::random(6));
        }

        $video->update($updateData);
        return response()->json($video->load('course:course_id,title'));
    }

    /**
     * Remove the specified video if its parent course is owned by the instructor.
     */
    public function destroy(Video $video)
    {
        if (!$this->authorizeInstructorForCourse($video->course)) {
            return response()->json(['message' => 'Unauthorized. You do not own the course this video belongs to.'], 403);
        }

        // TODO: Consider if deleting a video should affect quiz attempts or other related data.
        $video->delete();
        return response()->json(null, 204);
    }
}
