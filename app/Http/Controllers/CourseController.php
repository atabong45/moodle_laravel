<?php

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
