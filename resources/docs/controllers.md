<?php
// app/Http/Controllers/CourseController.php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('teacher')->paginate(10);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:100',
            'summary' => 'nullable|string',
            'numsections' => 'required|integer|min:1',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date|after:startdate',
        ]);

        $course = Course::create($validated + ['teacher_id' => auth()->id()]);

        return redirect()->route('courses.index')
            ->with('success', 'Cours créé avec succès.');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);
        
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:100',
            'summary' => 'nullable|string',
            'numsections' => 'required|integer|min:1',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date|after:startdate',
        ]);

        $course->update($validated);

        return redirect()->route('courses.show', $course)
            ->with('success', 'Cours mis à jour avec succès.');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Cours supprimé avec succès.');
    }
}

// app/Http/Controllers/SectionController.php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Course $course)
    {
        $sections = $course->sections()->paginate(10);
        return view('sections.index', compact('course', 'sections'));
    }

    public function create(Course $course)
    {
        return view('sections.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $section = $course->sections()->create($validated);

        return redirect()->route('sections.show', $section)
            ->with('success', 'Section créée avec succès.');
    }

    public function show(Section $section)
    {
        return view('sections.show', compact('section'));
    }

    public function edit(Section $section)
    {
        return view('sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $section->update($validated);

        return redirect()->route('sections.show', $section)
            ->with('success', 'Section mise à jour avec succès.');
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('courses.sections.index', $section->course)
            ->with('success', 'Section supprimée avec succès.');
    }
}

// app/Http/Controllers/ModuleController.php
namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index(Section $section)
    {
        $modules = $section->modules()->paginate(10);
        return view('modules.index', compact('section', 'modules'));
    }

    public function create(Section $section)
    {
        return view('modules.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modname' => 'required|string|max:255',
            'modplural' => 'required|string|max:255',
            'downloadcontent' => 'required|boolean',
            'file_path' => 'required|string|max:255',
        ]);

        $module = $section->modules()->create($validated);

        return redirect()->route('modules.show', $module)
            ->with('success', 'Module créé avec succès.');
    }

    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    public function edit(Module $module)
    {
        return view('modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modname' => 'required|string|max:255',
            'modplural' => 'required|string|max:255',
            'downloadcontent' => 'required|boolean',
            'file_path' => 'required|string|max:255',
        ]);

        $module->update($validated);

        return redirect()->route('modules.show', $module)
            ->with('success', 'Module mis à jour avec succès.');
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->route('sections.modules.index', $module->section)
            ->with('success', 'Module supprimé avec succès.');
    }
}

// app/Http/Controllers/AssignmentController.php
namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Module $module)
    {
        $assignments = $module->assignments()->paginate(10);
        return view('assignments.index', compact('module', 'assignments'));
    }

    public function create(Module $module)
    {
        return view('assignments.create', compact('module'));
    }

    public function store(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duedate' => 'required|date',
            'attemptnumber' => 'required|integer|min:1',
        ]);

        $assignment = $module->assignments()->create($validated);

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Devoir créé avec succès.');
    }

    public function show(Assignment $assignment)
    {
        return view('assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        return view('assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duedate' => 'required|date',
            'attemptnumber' => 'required|integer|min:1',
        ]);

        $assignment->update($validated);

        return redirect()->route('assignments.show', $assignment)
            ->with('success', 'Devoir mis à jour avec succès.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('modules.assignments.index', $assignment->module)
            ->with('success', 'Devoir supprimé avec succès.');
    }
}

// app/Http/Controllers/SubmissionController.php
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Assignment $assignment)
    {
        $submissions = $assignment->submissions()->with('student')->paginate(10);
        return view('submissions.index', compact('assignment', 'submissions'));
    }

    public function create(Assignment $assignment)
    {
        return view('submissions.create', compact('assignment'));
    }

    public function store(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'file_path' => 'required|string|max:255',
            'status' => 'required|string|in:draft,submitted,graded',
        ]);

        $submission = $assignment->submissions()->create([
            ...$validated,
            'student_id' => auth()->id(),
        ]);

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Soumission créée avec succès.');
    }

    public function show(Submission $submission)
    {
        return view('submissions.show', compact('submission'));
    }

    public function edit(Submission $submission)
    {
        $this->authorize('update', $submission);
        return view('submissions.edit', compact('submission'));
    }

    public function update(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);
        
        $validated = $request->validate([
            'file_path' => 'required|string|max:255',
            'status' => 'required|string|in:draft,submitted,graded',
        ]);

        $submission->update($validated);

        return redirect()->route('submissions.show', $submission)
            ->with('success', 'Soumission mise à jour avec succès.');
    }

    public function destroy(Submission $submission)
    {
        $this->authorize('delete', $submission);
        
        $submission->delete();

        return redirect()->route('assignments.submissions.index', $submission->assignment)
            ->with('success', 'Soumission supprimée avec succès.');
    }
}

// app/Http/Controllers/GradeController.php
namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Submission $submission)
    {
        $this->authorize('viewAny', Grade::class);
        
        $grade = $submission->grade;
        return view('grades.index', compact('submission', 'grade'));
    }

    public function create(Submission $submission)
    {
        $this->authorize('create', Grade::class);
        return view('grades.create', compact('submission'));
    }

    public function store(Request $request, Submission $submission)
    {
        $this->authorize('create', Grade::class);
        
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
        ]);

        $grade = $submission->grade()->create([
            ...$validated,
            'teacher_id' => auth()->id(),
        ]);

        $submission->update(['status' => 'graded']);

        return redirect()->route('grades.show', $grade)
            ->with('success', 'Note attribuée avec succès.');
    }

    public function show(Grade $grade)
    {
        $this->authorize('view', $grade);
        return view('grades.show', compact('grade'));
    }

    public function edit(Grade $grade)
    {
        $this->authorize('update', $grade);
        return view('grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $this->authorize('update', $grade);
        
        $validated = $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
        ]);

        $grade->update($validated);

        return redirect()->route('grades.show', $grade)
            ->with('success', 'Note mise à jour avec succès.');
    }

    public function destroy(Grade $grade)
    {
        $this->authorize('delete', $grade);
        
        $submission = $grade->submission;
        $grade->delete();
        
        $submission->update(['status' => 'submitted']);

        return redirect()->route('submissions.grades.index', $submission)
            ->with('success', 'Note supprimée avec succès.');
    }
}