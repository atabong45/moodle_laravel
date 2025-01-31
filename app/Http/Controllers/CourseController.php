<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\MoodleCourseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class CourseController extends Controller
{
    protected $moodleCourseService;

        public function __construct(MoodleCourseService $moodleCourseService)
    {
        $this->moodleCourseService = $moodleCourseService;
    }


    public function index(Request $request)
    {
        $search = $request->get('search'); // To filter by name
        // Fetch courses from Moodle
    //     $moodleCourses = $this->moodleCourseService->getAllCourses();
    //         // Optionally, save Moodle courses to the local database
    //         foreach ($moodleCourses as $moodleCourse) {
    //         Course::updateOrCreate(
    //             ['id' => $moodleCourse['id']],
    //             [
    //             'fullname' => $moodleCourse['fullname'],
    //             'shortname' => $moodleCourse['shortname'],
    //             'summary' => $moodleCourse['summary'],
    //             'numsections' => $moodleCourse['numsections'],
    //             'startdate' => $moodleCourse['startdate'] == 0 ? now() : $moodleCourse['startdate'],
    //             'enddate' => $moodleCourse['enddate'] == 0 ? now() : $moodleCourse['enddate'],
    //             ]
    //             );  
    // }

        $courses = Course::when($search, function ($query, $search) {
            return $query->where('fullname', 'like', '%' . $search . '%');
        })->with('teacher')->get();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
    return view('courses.create', compact('categories'));
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



        // Save the course in the local database
        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $course->load('sections.modules');
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
            'startdate' => 'nullable|date',
            'enddate' => 'nullable|date|after_or_equal:startdate',
            'teacher_id' => 'nullable|exists:users,id',
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
