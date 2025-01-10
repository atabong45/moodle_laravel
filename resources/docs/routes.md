<?php
// routes/web.php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\GradeController;

Route::middleware(['auth'])->group(function () {
    // Courses routes
    Route::resource('courses', CourseController::class);
    
    // Sections routes
    Route::resource('courses.sections', SectionController::class)->shallow();
    
    // Modules routes
    Route::resource('sections.modules', ModuleController::class)->shallow();
    
    // Assignments routes
    Route::resource('modules.assignments', AssignmentController::class)->shallow();
    
    // Submissions routes
    Route::resource('assignments.submissions', SubmissionController::class)->shallow();
    
    // Grades routes
    Route::resource('submissions.grades', GradeController::class)->shallow();
});