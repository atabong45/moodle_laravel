<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\MoodleCourseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    protected $moodleCourseService;

        public function __construct(MoodleCourseService $moodleCourseService)
    {
        $this->moodleCourseService = $moodleCourseService;
    }


    public function index(Request $request)
    {
    //     $search = $request->get('search'); // To filter by name
    //     // Fetch courses from Moodle
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

    //     $courses = Course::when($search, function ($query, $search) {
    //         return $query->where('fullname', 'like', '%' . $search . '%');
    //     })->with('teacher')->get();

        $courses = Course::with('courses.sections.modules')->get();

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
                'image' => 'nullable|image|max:2048',
            ]);
        } catch (ValidationException $e) {
            dd($e);
            return redirect()->route('courses.create')->with('error', 'Course not created ! Check parameters');
        }

       $moodleCourse = $this->moodleCourseService->createCourse($validated);
        dd($moodleCourse);
       $validated['teacher_id'] = Auth::id();
        // Check if the Moodle course creation was successful
        if (isset($moodleCourse['exception']) || isset($moodleCourse['errorcode'])) {
            return redirect()->route('courses.create')->with('error', 'Failed to create course in Moodle: ' . $moodleCourse['message']);
        }

        // save the image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne photo si elle existe
                if ($course->profile_picture) {
                    // Utilisez Storage pour supprimer l'ancienne image
                    Storage::disk('public')->delete($course->profile_picture);
                }

                // Enregistrer la nouvelle photo
                $path = $request->file('image')->store('courses_images', 'public');
                $course->profile_picture = $path;
            }
        // Save the course in the local database
        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $sections = $course->sections();

        return view('courses.show', compact('course', 'sections'));
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
            'teacher_id' => 'nullable|exists:courses,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // save the image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne photo si elle existe
            if ($course->profile_picture) {
                // Utilisez Storage pour supprimer l'ancienne image
                Storage::disk('public')->delete($course->profile_picture);
            }

            // Enregistrer la nouvelle photo
            $path = $request->file('image')->store('courses_images', 'public');
            $course->profile_picture = $path;
        }

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
