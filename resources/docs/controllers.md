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

// Les autres contrôleurs suivront le même modèle. Voici un exemple pour SectionController :

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

// Les autres contrôleurs (Mo