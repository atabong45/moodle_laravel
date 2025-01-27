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

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

    // Routes pour les administrateurs
    Route::middleware(['auth', 'role:ROLE_ADMIN'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [AdminController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    });

// Routes nécessitant une authentification
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    // Profile (accessible à tous les utilisateurs authentifiés)
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });



    // Routes pour les enseignants
    Route::middleware('role:ROLE_TEACHER')->group(function () {
        // Gestion des cours
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
        
        // Gestion des modules
        Route::resource('modules', ModuleController::class)->except(['index', 'show']);
        
        // Gestion des sections
        Route::resource('sections', SectionController::class)->except(['index', 'show']);
        
        // Gestion des évaluations
        Route::resource('assignments', AssignmentController::class)->except(['index', 'show']);
        
        // Gestion des notes
        Route::resource('grades', GradeController::class)->except(['index', 'show']);
    });

    // Routes accessibles aux enseignants ET aux étudiants
    Route::middleware('role:ROLE_TEACHER|ROLE_STUDENT')->group(function () {
        // Lecture seule pour les cours
        Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');
        
        // Lecture seule pour les modules
        Route::get('modules', [ModuleController::class, 'index'])->name('modules.index');
        Route::get('modules/{module}', [ModuleController::class, 'show'])->name('modules.show');
        
        // Lecture seule pour les sections
        Route::get('sections', [SectionController::class, 'index'])->name('sections.index');
        Route::get('sections/{section}', [SectionController::class, 'show'])->name('sections.show');
        
        // Lecture seule pour les évaluations
        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    });

    // Routes spécifiques aux étudiants
    Route::middleware('role:ROLE_STUDENT')->group(function () {
        Route::resource('submissions', SubmissionController::class);
        Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('grades/{grade}', [GradeController::class, 'show'])->name('grades.show');
    });

    // Routes pour la gestion des catégories (admin et profs)
    Route::middleware('role:ROLE_ADMIN|ROLE_TEACHER')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    });

    // Routes publiques pour les catégories (lecture seule)
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('categories/{id}/courses', [CategoryController::class, 'getCourses']);
});

require __DIR__.'/auth.php';