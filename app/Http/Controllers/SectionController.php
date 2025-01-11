<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Course;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with('course')->get();
        return view('sections.index', compact('sections'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('sections.create', compact('courses'));
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

    public function show(Section $section)
    {
        $section->load('course', 'modules');
        return view('sections.show', compact('section'));
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
}
