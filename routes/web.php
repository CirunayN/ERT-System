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
    Route::get('/history', [IncidentController::class, 'history'])->name('incidents.history');
    Route::resource('incidents', IncidentController::class);
    Route::get('/incidents/{incident}/manage', [IncidentController::class, 'manage'])
        ->name('incidents.manage')->middleware('role:responder,dispatcher,admin');
    Route::post('/incidents/{incident}/assign', [IncidentController::class, 'assignTeam'])
        ->name('incidents.assign')->middleware('role:dispatcher,admin');
    Route::post('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])
        ->name('incidents.updateStatus');
    Route::post('/incidents/{incident}/verify', [IncidentController::class, 'verify'])
        ->name('incidents.verify')->middleware('role:dispatcher,admin');

    // Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');

    // Notifications
    Route::post('/notifications/{id}/read', function ($id) {
        if ($notification = auth()->user()->notifications()->find($id)) {
            $notification->markAsRead();
            return redirect($notification->data['url'] ?? url()->previous());
        }
        return back();
    })->name('notifications.read');

    // Team Management (Admin and Dispatcher)
    Route::middleware('role:admin,dispatcher')->group(function () {
        Route::resource('teams', TeamController::class);
        Route::post('/teams/{team}/responders', [TeamController::class, 'addResponder'])->name('teams.addResponder');
        Route::delete('/teams/{team}/responders/{member}', [TeamController::class, 'removeResponder'])->name('teams.removeResponder');
    });

    // User Management (Admin)
    Route::middleware('role:admin')->group(function () {
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
