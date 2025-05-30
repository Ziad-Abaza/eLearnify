<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController as AdminCourseController;
use App\Http\Controllers\Api\VideoController as AdminVideoController;
use App\Http\Controllers\Api\QuestionController as AdminQuestionController;
use App\Http\Controllers\Api\QuestionOptionController as AdminQuestionOptionController;
use App\Http\Controllers\Api\UserCourseProgressController as AdminUserCourseProgressController;
use App\Http\Controllers\Api\UserVideoProgressController as AdminUserVideoProgressController;
use App\Http\Controllers\Api\QuizAttemptController as AdminQuizAttemptController;
use App\Http\Controllers\Api\CertificateController as AdminCertificateController;

// User-facing Controllers
use App\Http\Controllers\Api\User\CourseController as UserCourseController;
use App\Http\Controllers\Api\User\ProgressController as UserProgressController;
use App\Http\Controllers\Api\User\VideoController as UserVideoController;
use App\Http\Controllers\Api\User\QuizController as UserQuizController;

// Instructor-facing Controllers
use App\Http\Controllers\Api\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Api\Instructor\VideoController as InstructorVideoController;
use App\Http\Controllers\Api\Instructor\QuestionController as InstructorQuestionController;
use App\Http\Controllers\Api\Instructor\QuestionOptionController as InstructorQuestionOptionController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::get('/user', [AuthController::class, 'user']); // Duplicates the one outside, commented out

    // User-facing API routes
    Route::get('/courses', [UserCourseController::class, 'index']);
    Route::get('/courses/{course}', [UserCourseController::class, 'show']);
    Route::post('/courses/{course}/enroll', [UserCourseController::class, 'enroll']);

    // User Progress Routes
    Route::get('/me/courses', [UserProgressController::class, 'enrolledCourses']);
    Route::get('/me/courses/{course}/progress', [UserProgressController::class, 'courseProgress']);
    Route::post('/me/videos/{video}/complete', [UserProgressController::class, 'markVideoComplete']);

    // User Video Viewing
    Route::get('/videos/{video}', [UserVideoController::class, 'show']);

    // User Quiz Routes
    Route::get('/videos/{video}/quiz', [UserQuizController::class, 'showQuizForVideo']);
    Route::post('/videos/{video}/quiz/submit', [UserQuizController::class, 'submitQuizAnswers']);
    Route::get('/me/quiz-attempts', [UserQuizController::class, 'listUserAttempts']);
    Route::get('/quiz-attempts/{quizAttempt}', [UserQuizController::class, 'showUserAttempt']);
    // Add more user-facing routes here (e.g., progress, etc.)

    // Instructor routes
    Route::middleware(['instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::apiResource('courses', InstructorCourseController::class);
        Route::apiResource('courses.videos', InstructorVideoController::class)->shallow();
        Route::apiResource('videos.questions', InstructorQuestionController::class)->shallow();
        Route::apiResource('questions.options', InstructorQuestionOptionController::class)->shallow();
        // Add other instructor resources here (videos, questions for their courses etc.)
    });

    // Admin routes with combined middleware
    Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
        // User management
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);

        // Role management
        Route::get('/roles', [AdminController::class, 'getRoles']);
        Route::post('/roles', [AdminController::class, 'createRole']);
        Route::post('/roles/{role}', [AdminController::class, 'updateRole']);
        Route::delete('/roles/{role}', [AdminController::class, 'deleteRole']);

        // Admin Resource Management (using aliased controllers)
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('courses', AdminCourseController::class);
        Route::apiResource('videos', AdminVideoController::class);
        Route::apiResource('questions', AdminQuestionController::class);
        Route::apiResource('question-options', AdminQuestionOptionController::class);
        Route::apiResource('user-course-progress', AdminUserCourseProgressController::class);
        Route::apiResource('user-video-progress', AdminUserVideoProgressController::class);
        Route::apiResource('quiz-attempts', AdminQuizAttemptController::class);
        Route::apiResource('certificates', AdminCertificateController::class);
    });
});
