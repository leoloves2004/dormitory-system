<?php

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\Admin\RoomApplicationController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\VisitorLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {

    Route::get('/login', [
        AuthController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ]);

    Route::get('/register', [
        AuthController::class,
        'showRegister'
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register'
    ]);
});

Route::middleware('auth')->group(function (): void {

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    )->name('logout');

    Route::post(
        '/dark-mode',
        [AuthController::class, 'toggleDarkMode']
    )->name('dark-mode');

    Route::middleware('role:student')
        ->prefix('student')
        ->name('student.')
        ->group(function (): void {

            Route::get(
                '/dashboard',
                [StudentPortalController::class, 'dashboard']
            )->name('dashboard');

            Route::post(
                '/apply',
                [StudentPortalController::class, 'apply']
            )->name('apply');

            Route::put(
                '/profile',
                [StudentPortalController::class, 'profile']
            )->name('profile');

            Route::post(
                '/maintenance',
                [MaintenanceRequestController::class, 'store']
            )->name('maintenance.store');
        });

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function (): void {

            Route::get(
                '/dashboard',
                [DashboardController::class, 'admin']
            )->name('dashboard');

            Route::get(
                '/maintenance',
                [MaintenanceRequestController::class, 'index']
            )->name('maintenance.index');

            Route::post(
                '/maintenance',
                [MaintenanceRequestController::class, 'store']
            )->name('maintenance.store');

            Route::resource(
                'rooms',
                RoomController::class
            )->except(['create', 'show', 'edit']);

            Route::resource(
                'students',
                StudentController::class
            )->except(['create', 'show', 'edit']);

            Route::resource(
                'tenants',
                TenantController::class
            )->except(['create', 'show', 'edit']);

            Route::resource(
                'payments',
                PaymentController::class
            )->except(['create', 'show', 'edit']);

            Route::resource(
                'applications',
                RoomApplicationController::class
            )
            ->parameters([
                'applications' => 'roomApplication'
            ])
            ->except([
                'create',
                'show',
                'edit',
                'store'
            ]);

            Route::resource(
                'visitor-logs',
                VisitorLogController::class
            )->except([
                'create',
                'show',
                'edit'
            ]);

            Route::get(
                'reports',
                [ReportController::class, 'index']
            )->name('reports.index');

            Route::get(
                'reports/{type}/{format}',
                [ReportController::class, 'export']
            )->name('reports.export');

            Route::post(
                'imports/students',
                [ImportController::class, 'students']
            )->name('imports.students');

            Route::post(
                'imports/payments',
                [ImportController::class, 'payments']
            )->name('imports.payments');
        });
});
