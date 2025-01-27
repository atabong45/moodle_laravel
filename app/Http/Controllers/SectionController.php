<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Course;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Course $course)
    {
        $sections = $course->sections();
        return view('sections.index', compact('sections'));
    }

    public function create(Course $course)
    {
        $courses = Course::all();
        return view('sections.create', compact('course', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        Section::create($request->all());
        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }

    public function show(Course $course, Section $section)
    {
        $section->load('course', 'modules');
        
        $sections = $course->sections();
        $selectedSection = $section;

        return view('sections.show', compact('course', 'sections', 'selectedSection'));
    }

    public function edit(Section $section)
    {
        $courses = Course::all();
        return view('sections.edit', compact('section', 'courses'));
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        $section->update($request->all());
        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('sections.index')->with('success', 'Section deleted successfully.');
    }

    public function create_for_teacher($course_id)
    {
        $course = Course::findOrFail($course_id);
        return view('sections.create', compact('course'));
    }

    public function store_for_teacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
        ]);

        Section::create($request->all());
        return redirect()->route('courses.show', $request->course_id)->with('success', 'Section created successfully.');
    }
}
