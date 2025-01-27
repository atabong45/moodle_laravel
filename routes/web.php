<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;



// Welcome route (accessible sans authentification)
Route::get('/', function () {
    return view('welcome');
});

// Group of routes requiring authentication
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Administration (requires ROLE_ADMIN)
    Route::prefix('admin')->middleware('role:ROLE_ADMIN')->group(function () {
        Route::get('/create-user', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/create-user', [AdminController::class, 'store'])->name('admin.store');
    });

    // Assignments
    Route::resource('assignments', AssignmentController::class);

    // Courses
    Route::resource('courses', CourseController::class)->middleware('auth');

    //grades
    Route::resource('grades', GradeController::class);

    //modules
    Route::resource('modules', ModuleController::class);

    // Sections
    Route::resource('sections', SectionController::class);

    // Submissions
    Route::resource('submissions', SubmissionController::class);

    // Users
    Route::resource('users', UserController::class);

    //Categories
    Route::resource('categories', CategoryController::class);

    // Route pour obtenir les cours d'une catégorie
    Route::get('categories/{id}/courses', [CategoryController::class, 'getCourses']);

       // Routes pour les administrateurs
       Route::middleware(['auth', 'role:ROLE_ADMIN'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    });

});



// Include authentication routes
require __DIR__.'/auth.php';
