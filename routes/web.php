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
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubmissionQuestionController;


// Welcome route (accessible sans authentification)
Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    Route::post('/assignments/{assignment}/toggle-publish', [AssignmentController::class, 'togglePublish'])
    ->name('assignments.togglePublish');
    // Routes pour les questions d'un assignment
    Route::get('assignments/{assignment}/questions/edit', [AssignmentController::class, 'editQuestions'])->name('assignments.questions.edit');
    Route::put('assignments/{assignment}/questions/update', [AssignmentController::class, 'updateQuestions'])->name('assignments.questions.update');
    

    Route::resource('sections', SectionController::class);

    // Courses
    Route::resource('courses', CourseController::class);

    // Grades
    Route::resource('grades', GradeController::class);

    // Modules
    Route::resource('modules', ModuleController::class);
    Route::get('/modules/download/{module}', [ModuleController::class, 'download'])->name('modules.download');

    // Sections (nested under courses)
    Route::prefix('/courses/{course}')->group(function () {
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index'); // List all sections of a course
        Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create'); // Form to create a section
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store'); // Store a new section
        Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show'); // Show a specific section
        Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit'); // Form to edit a section
        Route::patch('/sections/{section}', [SectionController::class, 'update'])->name('sections.update'); // Update a section
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy'); // Delete a section
    });

    // Route::get('/sections/create_for_teacher/{course_id}', [SectionController::class, 'create_for_teacher'])->name('sections.create');
    // Route::post('/sections/store_for_teacher/', [SectionController::class, 'store_for_teacher'])->name('sections.store');
    // Submissions
    Route::resource('submissions', SubmissionController::class);

    // questions d'une soumission
    // Routes pour les soumissions
    Route::resource('submissions', SubmissionController::class);

    // Routes pour les questions de soumission
    Route::post('submissions/{submission}/questions', [SubmissionQuestionController::class, 'store'])
        ->name('submissions.questions.store');



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

    // Routes pour les questions
    Route::resource('/questions', QuestionController::class);


});

    Route::middleware(['auth', 'role:ROLE_STUDENT'])->group(function () {
        Route::get('assignments/{assignment}/compose', [AssignmentController::class, 'compose'])
            ->name('assignments.compose');
        Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'submit'])
            ->name('assignments.submit');
    });



// Include authentication routes
require __DIR__.'/auth.php';
