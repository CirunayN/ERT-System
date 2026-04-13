<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AnalyticsController;

// Public
Route::get('/', function () { return view('welcome'); })->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Incidents CRUD
    Route::resource('incidents', IncidentController::class);
    Route::post('/incidents/{incident}/assign', [IncidentController::class, 'assignTeam'])
        ->name('incidents.assign')->middleware('role:dispatcher,admin');

    // Team Management (Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('teams', TeamController::class);
        Route::post('/teams/{team}/responders', [TeamController::class, 'addResponder'])->name('teams.addResponder');
        Route::delete('/teams/{team}/responders/{responder}', [TeamController::class, 'removeResponder'])->name('teams.removeResponder');

        // User Management (Admin)
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
        Route::put('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Assignments (Dispatcher + Admin)
    Route::middleware('role:dispatcher,admin')->group(function () {
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    });

    // Analytics (Dispatcher + Admin)
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics')->middleware('role:dispatcher,admin');
});
