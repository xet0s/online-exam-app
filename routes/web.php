<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamPeriodController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\BuildingController;
use App\Models\Department;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegistrationController::class, 'register']);
});

Route::get('/pending-approval', function () {
    return view('auth.pending');
})->name('pending-approval');

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('approved')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('classrooms', ClassroomController::class)->except(['show']);

        Route::get('/exams/check-availability', [ExamController::class, 'checkAvailability'])->name('exams.check-availability');
        Route::get('/exams/suggest-datetime', [ExamController::class, 'suggestDateTime'])->name('exams.suggest-datetime');
        Route::post('/exams/approve-chair/{exam}', [ExamController::class, 'approveByChair'])->name('exams.approve-chair');
        Route::post('/exams/approve-dean/{exam}', [ExamController::class, 'approveByDean'])->name('exams.approve-dean');
        Route::resource('exams', ExamController::class)->except(['show']);

        // Sınav Haftası Yönetimi
        Route::post('/exam-periods', [ExamPeriodController::class, 'store'])->name('exam-periods.store');
        Route::delete('/exam-periods/{examPeriod}', [ExamPeriodController::class, 'destroy'])->name('exam-periods.destroy');
        Route::get('/exam-periods/for-department', [ExamPeriodController::class, 'getForDepartment'])->name('exam-periods.for-department');

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);

        Route::resource('departments', DepartmentController::class)->except(['show']);
        Route::resource('buildings', BuildingController::class)->except(['show']);

        Route::any('/admin/phpmyadmin/{path?}', [\App\Http\Controllers\Admin\PhpMyAdminProxyController::class, 'proxy'])
            ->where('path', '.*')
            ->name('admin.phpmyadmin');

        Route::prefix('approvals')->name('approvals.')->group(function () {
            Route::get('/', [ApprovalController::class, 'index'])->name('index');
            Route::post('/approve/{targetUser}', [ApprovalController::class, 'approve'])->name('approve');
            Route::post('/reject/{targetUser}', [ApprovalController::class, 'reject'])->name('reject');
            Route::post('/pre-approved', [ApprovalController::class, 'storePreApproved'])->name('pre-approved');
        });

        Route::post('/allocation/run', [AllocationController::class, 'run'])->name('allocation.run');

        Route::post('/pdf/export', [PdfExportController::class, 'export'])->name('pdf.export');
        Route::get('/pdf/download', [PdfExportController::class, 'download'])->name('pdf.download');
    });
});
