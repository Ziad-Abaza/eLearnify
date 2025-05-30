<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVideoProgress;
use App\Models\User;

class VideoController extends Controller
{
    /**
     * Display the specified video, its course, and user's progress on it.
     */
    public function show(Request $request, Video $video)
    {
        /** @var User $user */
        $user = Auth::user();

        // Load video with course information
        $video->load('course:course_id,title');

        // Check if user is enrolled in the course this video belongs to
        if ($video->course) {
            $currentUser = Auth::user();
            if ($currentUser instanceof User) {
                $isEnrolled = $currentUser->userCourseProgress()->where('course_id', $video->course->course_id)->exists();
                if (!$isEnrolled) {
                    return response()->json(['message' => 'You must be enrolled in the course to view this video.'], 403);
                }
            } else {
                // Handle case where Auth::user() does not return an instance of App\Models\User (should not happen in normal flow)
                return response()->json(['message' => 'Authentication error.'], 500);
            }
        } else {
            // This case should ideally not happen if videos are always linked to courses
            return response()->json(['message' => 'Video is not associated with a course.'], 404);
        }

        // Get user's progress for this specific video
        $videoProgress = UserVideoProgress::where('user_id', $user->user_id)
                                          ->where('video_id', $video->video_id)
                                          ->first();

        // Get other videos in the same course, ordered, for playlist functionality
        $courseVideos = Video::where('course_id', $video->course_id)
                             ->orderBy('order_in_course', 'asc')
                             ->select('video_id', 'title', 'order_in_course') // Select minimal fields
                             ->get();

        return response()->json([
            'video' => $video,
            'user_progress' => $videoProgress, // Could be null if user hasn't started it
            'playlist' => $courseVideos // Videos in the same course for navigation
        ]);
    }
}
