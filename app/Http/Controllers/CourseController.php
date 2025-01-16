<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search'); // To filter by name

        $courses = Course::when($search, function ($query, $search) {
            return $query->where('fullname', 'like', '%' . $search . '%');
        })->with('teacher')->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'fullname' => 'required|string|max:255',
                'shortname' => 'required|string|max:255',
                'summary' => 'nullable|string',
                'numsections' => 'required|integer',
                'startdate' => 'required|date',
                'enddate' => 'nullable|date|after_or_equal:startdate',
            ]);
        } catch (ValidationException $e) {
            dd($e);
            return redirect()->route('courses.create')->with('error', 'Course not created ! Check parameters');
        }

        $validated['teacher_id'] = Auth::id();

        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'numsections' => 'required|integer',
            'startdate' => 'required|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
