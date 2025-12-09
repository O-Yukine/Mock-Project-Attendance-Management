<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\AuthController;


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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'destroy']);

Route::get('/attendance', [AttendanceController::class, 'index']);
Route::post('/attendance', [AttendanceController::class, 'startWorking']);


Route::get('/attendance/list', [AttendanceController::class, 'showList']);
Route::get('/attendance/detail', [AttendanceController::class, 'showDetail']);
Route::get('/stamp_correction_request/list', [AttendanceController::class, 'showRequest']);

Route::get('/admin/attendance/list', [AdminController::class, 'showList']);
Route::get('/admin/attendance/detail', [AdminController::class, 'showDetail']);
Route::get('/admin/staff/list', [AdminController::class, 'showStaff']);
Route::get('/admin/attendance/staff/detail', [AdminController::class, 'showStaffAttendanceList']);

Route::get('/stamp_correction_request/approve/detail', [AdminController::class, 'requestApprove']);
