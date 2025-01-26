<?php
// app/Http/Controllers/MoodleCourseController.php

namespace App\Http\Controllers;

use App\Services\MoodleCourseService;
use Illuminate\Http\Request;

class MoodleCourseController extends Controller
{
    protected $courseService;

    public function __construct(MoodleCourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        try {
            $courses = $this->courseService->getAllCourses();
            return view('courses.index', [
                'courses' => $courses,
                'message' => 'Liste des cours récupérée avec succès'
            ]);
        } catch (\Exception $e) {
            return view('courses.index', [
                'courses' => [],
                'error' => 'Erreur lors de la récupération des cours: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try {
            $contents = $this->courseService->getCourseContents($id);
            return view('courses.show', [
                'contents' => $contents,
                'message' => 'Contenu du cours récupéré avec succès'
            ]);
        } catch (\Exception $e) {
            return view('courses.show', [
                'contents' => [],
                'error' => 'Erreur lors de la récupération du contenu: ' . $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        try {
            $categories = $this->courseService->getCategories();
            return view('courses.create', [
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return view('courses.create', [
                'categories' => [],
                'error' => 'Erreur lors de la récupération des catégories: ' . $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:100',
            'categoryid' => 'required|integer'
        ]);

        try {
            $courseData = $request->only([
                'fullname', 'shortname', 'categoryid', 'summary',
                'format', 'showgrades', 'newsitems', 'startdate', 'enddate'
            ]);

            $newCourse = $this->courseService->createCourse($courseData);
            return redirect()->route('courses.index')
                ->with('success', 'Cours créé avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du cours: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $courses = $this->courseService->getAllCourses();
            $course = collect($courses)->firstWhere('id', $id);
            $categories = $this->courseService->getCategories();

            return view('courses.edit', [
                'course' => $course,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                ->with('error', 'Erreur lors de la récupération du cours: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'shortname' => 'required|string|max:100',
            'categoryid' => 'required|integer'
        ]);

        try {
            $courseData = $request->only([
                'fullname', 'shortname', 'categoryid', 'summary',
                'format', 'showgrades', 'newsitems', 'startdate', 'enddate'
            ]);

            $this->courseService->updateCourse($id, $courseData);
            return redirect()->route('courses.index')
                ->with('success', 'Cours mis à jour avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du cours: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->courseService->deleteCourse($id);
            return redirect()->route('courses.index')
                ->with('success', 'Cours supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                ->with('error', 'Erreur lors de la suppression du cours: ' . $e->getMessage());
        }
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'course_id' => 'required|integer',
            'role_id' => 'required|integer'
        ]);

        try {
            $this->courseService->enrollUser(
                $request->user_id,
                $request->course_id,
                $request->role_id
            );
            
            return redirect()->back()
                ->with('success', 'Utilisateur inscrit avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'inscription: ' . $e->getMessage());
        }
    }

    public function unenroll(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'course_id' => 'required|integer'
        ]);

        try {
            $this->courseService->unenrollUser(
                $request->user_id,
                $request->course_id
            );
            
            return redirect()->back()
                ->with('success', 'Utilisateur désinscrit avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la désinscription: ' . $e->getMessage());
        }
    }
}