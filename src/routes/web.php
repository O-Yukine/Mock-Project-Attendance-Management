<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\StampCorrectionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/email/verify', [VerifyEmailController::class, 'index'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [VerifyEmailController::class, 'resendVerificationEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/auth/register', [AuthController::class, 'showRegister']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/auth/login', [AuthController::class, 'showLogin']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'destroy']);

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance', [AttendanceController::class, 'updateAttendance']);
    Route::get('/attendance/list', [AttendanceController::class, 'showList']);
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'showDetail']);
    Route::post('/attendance/detail/{id}', [AttendanceController::class, 'updateDetail']);
    // Route::get('/stamp_correction_request/list', [AttendanceController::class, 'showRequest']);
});

Route::get('/auth/admin-login', [AdminAuthController::class, 'showLogin']);
Route::post('/auth/admin-login', [AdminAuthController::class, 'login']);
Route::post('/auth/admin-logout', [AdminAuthController::class, 'destroy']);

Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/attendance/list', [AdminController::class, 'showAttendanceList']);
    Route::get('/admin/attendance/{id}', [AdminController::class, 'showDetail']);
    Route::patch('/admin/attendance/{id}', [AdminController::class, 'updateDetail']);
    Route::get('/admin/staff/list', [AdminController::class, 'showStaffList']);
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'showStaffAttendanceList']);
    // Route::get('/stamp_correction_request/list', [AdminController::class, 'showRequest']);
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [StampCorrectionController::class, 'requestShow']);
    Route::patch('/stamp_correction_request/approve/{attendance_correct_request_id}', [StampCorrectionController::class, 'requestApprove']);
});

Route::get('/stamp_correction_request/list', [StampCorrectionController::class, 'index']);
