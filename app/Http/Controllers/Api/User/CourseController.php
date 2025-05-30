<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of available courses.
     */
    public function index(Request $request)
    {
        // TODO: Add filtering by category, search term, instructor etc.
        // TODO: Add pagination
        // For now, just return all courses with basic info
        $courses = Course::with(['category', 'instructor:user_id,name']) // Select only name and id from instructor
                        ->withCount('videos') // Get a count of videos
                        // ->where('is_published', true) // Assuming you add an is_published flag to courses
                        ->orderBy('created_at', 'desc')
                        ->paginate(10); // Added pagination

        return response()->json($courses);
    }

    /**
     * Display the specified course details.
     */
    public function show(Course $course)
    {
        // Eager load necessary details for a single course view
        $course->load([
            'category',
            'instructor:user_id,name,email', // Include email for instructor if needed
            'videos' => function ($query) {
                $query->orderBy('order_in_course', 'asc')->select(['video_id', 'course_id', 'title', 'duration', 'order_in_course']); // Select specific fields for videos
            }
        ]);
        // TODO: Check if course is published or if user is enrolled if it's a private course

        return response()->json($course);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }

    /**
     * Enroll the authenticated user in the specified course.
     */
    public function enroll(Request $request, Course $course)
    {
        $user = $request->user(); // Or Auth::user()

        // Check if already enrolled by looking for a progress record
        $existingProgress = $user->userCourseProgress()->where('course_id', $course->course_id)->first();

        if ($existingProgress) {
            return response()->json(['message' => 'You are already enrolled in this course.'], 409); // Conflict
        }

        // Create a new progress record to signify enrollment
        $progress = $user->userCourseProgress()->create([
            // 'user_course_id' => Str::uuid()->toString(), // Handled by HasUuids trait on UserCourseProgress model
            'course_id' => $course->course_id,
            'completion_percentage' => 0,
            'last_accessed' => now(),
        ]);

        return response()->json([
            'message' => 'Successfully enrolled in the course.',
            'progress' => $progress->load('course') // Optionally return the new progress record
        ], 201);
    }

    // TODO: Add methods for getting user-specific course data (e.g., progress), etc.
}
