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

    // Profile management
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Administration (requires ROLE_ADMIN)
    Route::prefix('admin')->middleware('role:ROLE_ADMIN')->group(function () {
        Route::get('/create-user', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/create-user', [AdminController::class, 'store'])->name('admin.store');
    });

    // Assignments
    Route::resource('assignments', AssignmentController::class);

    // Courses
    Route::resource('courses', CourseController::class);

    // Grades
    Route::resource('grades', GradeController::class);

    // Modules
    Route::resource('modules', ModuleController::class);

    // Sections (nested under courses)
    Route::prefix('courses/{course}')->group(function () {
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index'); // List all sections of a course
        Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create'); // Form to create a section
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store'); // Store a new section
        Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show'); // Show a specific section
        Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit'); // Form to edit a section
        Route::patch('/sections/{section}', [SectionController::class, 'update'])->name('sections.update'); // Update a section
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy'); // Delete a section
    });

    // Submissions
    Route::resource('submissions', SubmissionController::class);

    // Users
    Route::resource('users', UserController::class);
});

// Include authentication routes
require __DIR__.'/auth.php';
