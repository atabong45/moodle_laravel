<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController; // Supposant que vous en ayez un
use App\Http\Controllers\EventController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SynchronisationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
|
| Ces routes sont accessibles par tout le monde, même les visiteurs non connectés.
|
*/

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');


/*
|--------------------------------------------------------------------------
| Routes Authentifiées
|--------------------------------------------------------------------------
|
| Ce groupe contient toutes les routes qui nécessitent que l'utilisateur soit connecté.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // --- Tableau de Bord ---
    // (Utiliser un contrôleur est une meilleure pratique que de mettre la logique ici)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Profil Utilisateur ---
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // --- Cours ---
    Route::resource('courses', CourseController::class);

    // --- Sections (imbriquées dans les cours pour la clarté) ---
    Route::resource('sections', SectionController::class);

    // --- Modules ---
    Route::resource('modules', ModuleController::class)->except(['index', 'show']); // La plupart du temps, les modules sont vus via les cours
    Route::get('/modules/download/{module}', [ModuleController::class, 'download'])->name('module.download');

    // --- Devoirs (Assignments) ---
    Route::resource('assignments', AssignmentController::class);
    Route::get('/assignments/{module}/submissions', [AssignmentController::class, 'submissions'])->name('assignments.submissions');
    Route::post('/assignments/{assignment}/toggle-publish', [AssignmentController::class, 'togglePublish'])->name('assignments.togglePublish');
    Route::get('assignments/{assignment}/questions/edit', [AssignmentController::class, 'editQuestions'])->name('assignments.questions.edit');
    Route::put('assignments/{assignment}/questions/update', [AssignmentController::class, 'updateQuestions'])->name('assignments.questions.update');

    // --- Soumissions (Submissions) ---
    Route::post('/submissions/{module}', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{module}', [SubmissionController::class, 'index'])->name('submissions.index'); // Ajout pour la vue des soumissions
    Route::post('/submissions/{submission}/grade', [SubmissionController::class, 'grade'])->name('submissions.grade');

    // --- Questions ---
    Route::resource('questions', QuestionController::class);

    // --- Catégories ---
    Route::resource('categories', CategoryController::class);
    Route::get('categories/{category}/courses', [CategoryController::class, 'getCourses'])->name('categories.courses');

    // --- Événements (Calendrier) ---
    Route::controller(EventController::class)->prefix('events')->name('events.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::delete('/{event}', 'destroy')->name('destroy');
    });

    // --- Synchronisation ---
    Route::post('/synchronisation', [SynchronisationController::class, 'synchronize'])->name('synchronisation');


    /*
    |--------------------------------------------------------------------------
    | Routes Spécifiques aux Rôles
    |--------------------------------------------------------------------------
    */

    // --- Routes pour les Administrateurs (ROLE_ADMIN) ---
    Route::middleware('role:ROLE_ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminController::class);
        // Vous pouvez ajouter d'autres routes admin ici
    });

    // --- Routes pour les Étudiants (ROLE_STUDENT) ---
    Route::middleware('role:ROLE_STUDENT')->group(function () {
        Route::post('assignments/{module}/compose', [AssignmentController::class, 'composeTest'])->name('assignments.compose');
        Route::post('assignments/{module}/grades', [AssignmentController::class, 'createGrade'])->name('grades.create');
    });

});

// Inclusion des routes d'authentification (login, register, etc.)
require __DIR__.'/auth.php';