<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\BaiTestController;
use App\Http\Controllers\CauHoiController;
use App\Http\Controllers\DapAnController;
use Illuminate\Support\Facades\Route;

// ============ Auth Routes (Public - Rate Limited) ============
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ============ Auth Routes (Protected) ============
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});

// ============ Lesson Routes (Public) ============
Route::get('/lessons', [LessonController::class, 'index']);
// IMPORTANT: Specific routes BEFORE generic {id} route to avoid conflicts
Route::get('/lessons/filter', [LessonController::class, 'filter']);
Route::get('/lessons/teacher/{teacherId}', [LessonController::class, 'getByTeacher']);
Route::get('/lessons/{id}', [LessonController::class, 'show']);

// ============ Lesson Routes (Teacher - Protected) ============
Route::middleware(['auth:sanctum', 'role:giao_vien,admin'])->group(function () {
    Route::post('/lessons', [LessonController::class, 'store']);
    // IMPORTANT: Specific routes BEFORE generic {id} route
    Route::get('/lessons/{id}/edit', [LessonController::class, 'showForTeacher']); // For teachers to edit
    Route::patch('/lessons/{id}/toggle-status', [LessonController::class, 'toggleStatus']);
    Route::put('/lessons/{id}', [LessonController::class, 'update']);
    Route::delete('/lessons/{id}', [LessonController::class, 'destroy']);
    Route::get('/teacher/lessons', [LessonController::class, 'myLessons']);
});

// ============ BaiTest Routes (Public) ============
Route::get('/lessons/{lessonId}/bai-tests', [BaiTestController::class, 'indexByLesson']);
// Moved show method to student protected block

// ============ BaiTest Routes (Teacher - Protected) ============
Route::middleware(['auth:sanctum', 'role:giao_vien,admin'])->group(function () {
    Route::post('/bai-tests', [BaiTestController::class, 'store']);
    Route::get('/bai-tests/{id}', [BaiTestController::class, 'showForTeacher']); // Add this line - for teachers to edit
    Route::put('/bai-tests/{id}', [BaiTestController::class, 'update']);
    Route::delete('/bai-tests/{id}', [BaiTestController::class, 'destroy']);
    Route::get('/teacher/bai-tests', [BaiTestController::class, 'myTests']);
    Route::get('/bai-tests/{id}/analytics', [BaiTestController::class, 'getAnalytics']);

    // CauHoi (Question) Routes
    Route::get('/bai-tests/{testId}/cau-hois', [CauHoiController::class, 'indexByTest']);
    Route::post('/bai-tests/{testId}/cau-hois', [CauHoiController::class, 'store']);
    Route::put('/bai-tests/{testId}/cau-hois/{questionId}', [CauHoiController::class, 'update']);
    Route::delete('/bai-tests/{testId}/cau-hois/{questionId}', [CauHoiController::class, 'destroy']);

    // DapAn (Answer) Routes
    Route::post('/bai-tests/{testId}/cau-hois/{questionId}/dap-ans', [DapAnController::class, 'store']);
    Route::put('/bai-tests/{testId}/cau-hois/{questionId}/dap-ans/{answerId}', [DapAnController::class, 'update']);
    Route::delete('/bai-tests/{testId}/cau-hois/{questionId}/dap-ans/{answerId}', [DapAnController::class, 'destroy']);
});

// ============ BaiTest Routes (Student - Protected) ============
Route::middleware(['auth:sanctum', 'role:hoc_sinh,admin'])->group(function () {
    Route::get('/bai-tests/{id}', [BaiTestController::class, 'show']); // Allow viewing test details
    Route::post('/bai-tests/{testId}/start', [BaiTestController::class, 'startTest']);
    Route::post('/bai-tests/{testId}/submit', [BaiTestController::class, 'submitTest']);
    Route::get('/bai-tests/{testId}/result', [BaiTestController::class, 'getResult']);
});

// ============ Admin Routes (Protected) - Stats & Test Management (Admin & Teacher) ============
Route::middleware(['auth:sanctum', 'role:giao_vien,admin'])->group(function () {
    Route::get('/admin/stats', [AdminController::class, 'getStats']);

    // Admin Test Management Routes
    Route::get('/admin/tests', [AdminController::class, 'getAllTests']);
    Route::get('/admin/tests/{id}', [AdminController::class, 'getTestDetail']);
    Route::post('/admin/tests', [AdminController::class, 'createTest']);
    Route::put('/admin/tests/{id}', [AdminController::class, 'updateTest']);
    Route::delete('/admin/tests/{id}', [AdminController::class, 'deleteTest']);
});

// ============ Admin Routes (Teacher Management - Admin Only) ============
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/admin/teachers', [AdminController::class, 'createTeacher']);
    Route::get('/admin/teachers', [AdminController::class, 'getTeachers']);
    Route::put('/admin/teachers/{id}', [AdminController::class, 'updateTeacher']);
    Route::delete('/admin/teachers/{id}', [AdminController::class, 'deleteTeacher']);
});
