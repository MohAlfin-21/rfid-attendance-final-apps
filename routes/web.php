<?php

use App\Http\Controllers\Admin\AbsenceRequestController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AttendanceAnalyticsController;
use App\Http\Controllers\Admin\AttendanceExportController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\UserCardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AbsenceRequestAttachmentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Smart redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('teacher')) return redirect()->route('teacher.dashboard');
    if ($user->hasRole('secretary')) return redirect()->route('secretary.dashboard');
    return redirect()->route('student.dashboard');
})->middleware('auth')->name('dashboard');

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('auth')->get(
    '/absence-requests/{absence_request}/attachment',
    AbsenceRequestAttachmentController::class
)->name('absence-requests.attachment');

// ── Admin Panel ──────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('users/{user}/cards', [UserCardController::class, 'store'])->name('users.cards.store');
    Route::post('users/{user}/cards/enrollment', [UserCardController::class, 'startEnrollment'])->name('users.cards.enrollment.start');
    Route::get('users/{user}/cards/enrollment-status', [UserCardController::class, 'enrollmentStatus'])->name('users.cards.enrollment.status');
    Route::delete('users/{user}/cards/enrollment-status', [UserCardController::class, 'cancelEnrollment'])->name('users.cards.enrollment.cancel');
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('classrooms', ClassroomController::class);
    Route::resource('devices', DeviceController::class);
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('attendances/{attendance}/override', [AttendanceController::class, 'override'])->name('attendances.override');
    Route::get('attendances/export', [AttendanceExportController::class, 'export'])->name('attendances.export');
    Route::get('analytics', [AttendanceAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('absence-requests', [AbsenceRequestController::class, 'index'])->name('absence-requests.index');
    Route::get('absence-requests/{absence_request}', [AbsenceRequestController::class, 'show'])->name('absence-requests.show');
    Route::put('absence-requests/{absence_request}', [AbsenceRequestController::class, 'update'])->name('absence-requests.update');
    Route::get('settings', [SystemSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SystemSettingController::class, 'update'])->name('settings.update');
});

// ── Teacher Panel ────────────────────────────────
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/classroom', [TeacherDashboardController::class, 'classroom'])->name('classroom');
    Route::get('/attendance', [TeacherDashboardController::class, 'attendance'])->name('attendance');
    Route::get('/absence-requests', [TeacherDashboardController::class, 'absenceRequests'])->name('absence-requests');
    Route::put('/absence-requests/{absence_request}', [TeacherDashboardController::class, 'reviewAbsenceRequest'])->name('absence-requests.review');
});

// ── Secretary Panel ─────────────────────────────
Route::middleware(['auth', 'role:secretary'])->prefix('secretary')->name('secretary.')->group(function () {
    Route::get('/', [App\Http\Controllers\Secretary\SecretaryDashboardController::class, 'index'])->name('dashboard');
    Route::get('/absence-requests', [App\Http\Controllers\Secretary\SecretaryDashboardController::class, 'absenceRequests'])->name('absence-requests');
    Route::put('/absence-requests/{absence_request}', [App\Http\Controllers\Secretary\SecretaryDashboardController::class, 'reviewAbsenceRequest'])->name('absence-requests.review');
});


// ── Student Panel ────────────────────────────────
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendance', [StudentDashboardController::class, 'attendance'])->name('attendance');
    Route::get('/absence-requests', [StudentDashboardController::class, 'absenceRequestsIndex'])->name('absence-requests.index');
    Route::get('/absence-requests/create', [StudentDashboardController::class, 'absenceRequestsCreate'])->name('absence-requests.create');
    Route::post('/absence-requests', [StudentDashboardController::class, 'absenceRequestsStore'])->name('absence-requests.store');
    Route::get('/leaderboard', [StudentDashboardController::class, 'leaderboard'])->name('leaderboard');
});

// ── Profile ──────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
