<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCourseProgress;
use App\Models\Video;
use App\Models\UserVideoProgress;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Display a listing of courses the authenticated user is enrolled in.
     */
    public function enrolledCourses(Request $request)
    {
        $user = Auth::user();

        $enrolledCourses = UserCourseProgress::where('user_id', $user->user_id)
            ->with([
                'course:course_id,title,description,thumbnail_url,category_id,instructor_id', // Select necessary course fields
                'course.category:category_id,name',
                'course.instructor:user_id,name'
            ])
            ->orderBy('last_accessed', 'desc')
            ->paginate(10);

        return response()->json($enrolledCourses);
    }

    /**
     * Display the authenticated user's progress for a specific course, including video progress.
     */
    public function courseProgress(Request $request, Course $course)
    {
        $user = Auth::user();

        // Find the specific course progress for the user
        $courseProgress = UserCourseProgress::where('user_id', $user->user_id)
            ->where('course_id', $course->course_id)
            ->with([
                'course:course_id,title', // Basic course info
                'userVideoProgress' => function ($query) {
                    $query->with('video:video_id,title,order_in_course') // Load video details for each progress entry
                          ->orderBy('video.order_in_course', 'asc');
                }
            ])
            ->first();

        if (!$courseProgress) {
            return response()->json(['message' => 'You are not enrolled in this course or no progress found.'], 404);
        }

        // Optionally, you could load all videos for the course and merge progress information if needed.
        // For now, we only return videos the user has progress on.

        return response()->json($courseProgress);
    }

    /**
     * Mark a video as completed for the authenticated user and update course progress.
     */
    public function markVideoComplete(Request $request, Video $video)
    {
        $user = Auth::user();
        $course = $video->course; // Get the course associated with the video

        if (!$course) {
            return response()->json(['message' => 'Video does not belong to a course.'], 400);
        }

        // Check if the user is enrolled in the course this video belongs to
        $userCourseProgress = UserCourseProgress::where('user_id', $user->user_id)
                                                ->where('course_id', $course->course_id)
                                                ->first();

        if (!$userCourseProgress) {
            return response()->json(['message' => 'You are not enrolled in this course.'], 403);
        }

        DB::beginTransaction();
        try {
            // Create or update video progress
            $videoProgress = UserVideoProgress::updateOrCreate(
                [
                    'user_id' => $user->user_id,
                    'video_id' => $video->video_id,
                ],
                [
                    'course_id' => $course->course_id, // Ensure course_id is set
                    'completed_at' => now(),
                    'progress_seconds' => $video->duration // Mark as fully watched
                ]
            );

            // Recalculate and update overall course progress
            $completedVideosCount = UserVideoProgress::where('user_id', $user->user_id)
                                                    ->where('course_id', $course->course_id)
                                                    ->whereNotNull('completed_at')
                                                    ->count();

            $totalVideosInCourse = $course->videos()->count();

            $completionPercentage = ($totalVideosInCourse > 0) ? ($completedVideosCount / $totalVideosInCourse) * 100 : 0;

            $userCourseProgress->update([
                'completion_percentage' => $completionPercentage,
                'last_accessed' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Video marked as complete.',
                'video_progress' => $videoProgress,
                'course_progress' => $userCourseProgress->fresh() // Return updated course progress
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to mark video as complete.', 'error' => $e->getMessage()], 500);
        }
    }
}
