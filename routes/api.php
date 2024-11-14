<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentScheduleController;
use App\Http\Controllers\CourseScheduleController;
use App\Http\Controllers\UserController;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::apiResource('courses', CourseController::class);
Route::apiResource('course-schedules', CourseScheduleController::class);
Route::apiResource('student-schedules', StudentScheduleController::class);
Route::apiResource('users', UserController::class);

Route::get('/students', [UserController::class, 'getAllStudents']);
Route::get('/lecturers', [UserController::class, 'getAllLecturers']);
Route::get('/course-schedules/{id}/students', [CourseScheduleController::class, 'getStudentsByCourseSchedule']);