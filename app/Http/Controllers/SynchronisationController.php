<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Models\Section;
use App\Models\Module;
use App\Services\MoodleCategoryService;
use App\Services\MoodleCourseService;
use App\Services\MoodleUserService;
use App\Services\MoodleSectionService;
use App\Services\MoodleModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SynchronisationController extends Controller
{
    protected $moodleCourseService;
    protected $moodleUserService;
    protected $moodleCategoryService;
    protected $moodleSectionService;
    protected $moodleModuleService;

    public function __construct(
        MoodleCourseService $moodleCourseService,
        MoodleUserService $moodleUserService,
        MoodleCategoryService $moodleCategoryService,
        MoodleSectionService $moodleSectionService,
        MoodleModuleService $moodleModuleService
    ) {
        $this->moodleCourseService = $moodleCourseService;
        $this->moodleUserService = $moodleUserService;
        $this->moodleCategoryService = $moodleCategoryService;
        $this->moodleSectionService = $moodleSectionService;
        $this->moodleModuleService = $moodleModuleService;
    }

    public function synchronize(Request $request)
    {
        try {
            // Vérifier la disponibilité du serveur Moodle
            if (!$this->moodleCourseService->isServerAvailable()) {
                return redirect()->back()->with('error', 'Le serveur Moodle n\'est pas disponible.');
            }

            // Synchronisation des catégories
            $moodleCategories = $this->moodleCategoryService->getToutesCategories();
            $moodleCategoryIds = array_column($moodleCategories, 'id');
            foreach ($moodleCategories as $moodleCategory) {
                Category::updateOrCreate(
                    ['id' => $moodleCategory['id']],
                    [
                        'name' => $moodleCategory['name'],
                    ]
                );
            }

            // Synchronisation des cours
            $moodleCourses = $this->moodleCourseService->getAllCourses();
            $moodleCourseIds = array_column($moodleCourses, 'id');

            foreach ($moodleCourses as $moodleCourse) {
                // Vérifiez que la catégorie existe avant de mettre à jour le cours
                if (in_array($moodleCourse['categoryid'], $moodleCategoryIds)) {
                    Course::updateOrCreate(
                        ['id' => $moodleCourse['id']],
                        [
                            'fullname' => $moodleCourse['fullname'],
                            'shortname' => $moodleCourse['shortname'],
                            'summary' => $moodleCourse['summary'],
                            'numsections' => $moodleCourse['numsections'],
                            'startdate' => $moodleCourse['startdate'] == 0 ? null : $moodleCourse['startdate'],
                            'enddate' => $moodleCourse['enddate'] == 0 ? null : $moodleCourse['enddate'],
                            'teacher_id' => $moodleCourse['teacher_id'] ?? null,
                            'category_id' => $moodleCourse['categoryid'],
                            'image' => $moodleCourse['image'] ?? null,
                        ]
                    );
                } else {
                    Log::warning('La catégorie avec l\'ID ' . $moodleCourse['categoryid'] . ' n\'existe pas.');
                }
            }

            // Récupérer les cours locaux qui ne sont pas présents sur Moodle
            $localCourses = Course::whereNotIn('id', $moodleCourseIds)->get();
            foreach ($localCourses as $localCourse) {
                $this->moodleCourseService->createCourse($localCourse);
            }
            $localCourses = Course::whereNotIn('id', $moodleCourseIds)->delete();


            // Synchronisation des utilisateurs
            $moodleUsers = $this->moodleUserService->getUsers();
            $moodleUserIds = array_column($moodleUsers['users'], 'id');

            foreach ($moodleUsers['users'] as $moodleUser) {
                User::updateOrCreate(
                    ['id' => $moodleUser['id']],
                    [
                        'name' => $moodleUser['fullname'],
                        'email' => $moodleUser['email'],
                        'password' => bcrypt('defaultpassword'), // Vous pouvez gérer les mots de passe différemment
                        'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                    ]
                );
            }

            $localUsers = User::whereNotIn('id', $moodleUserIds)->delete();

           // Synchronisation des sections depuis Moodle vers le client
            $moodleCourses = $this->moodleCourseService->getAllCourses();
            foreach ($moodleCourses as $moodleCourse) {
                $sections = $this->moodleSectionService->listerSectionsCours($moodleCourse['id']);
                foreach ($sections as $section) {
                    Section::updateOrCreate(
                        ['course_id' => $moodleCourse['id'], 'name' => $section['name']],
                        [
                            'name' => $section['name'],
                            'course_id' => $moodleCourse['id'],
                        ]
                    );

                    // Synchronisation des modules depuis Moodle vers le client
                    foreach ($section['modules'] as $module) {
                        if (Section::where(['course_id' => $moodleCourse['id'], 'name' => $section['name']])->exists()) {
                            Module::updateOrCreate(
                                ['section_id' => $section['id'],'name' => $module['name']],
                                [
                                    'name' => $module['name'],
                                    'modname' => $module['modname'],
                                    'modplural' => $module['modplural'] ?? '', 
                                    'downloadcontent' => $module['downloadcontent'] ?? '', 
                                    'file_path' => isset($module['contents'][0]['fileurl']) 
                                                        ? str_replace('?forcedownload=1', '?token=' . config('moodle.api_token'), $module['contents'][0]['fileurl']) 
                                                        : '',
                                    'section_id' => Section::where(['course_id' => $moodleCourse['id'], 'name' => $section['name']])->value('id'),
                                    'course_id' => $moodleCourse['id'],
                                ]
                            );
                        } else {
                            Log::warning('La section avec l\'ID ' . $section['id'] . ' n\'existe pas.');
                        }
                    }
                }
            }
            // Synchronisation des sections depuis le client vers Moodle
            $localSections = Section::whereNotIn('id', array_column($sections, 'id'))->get();
            foreach ($localSections as $localSection) {
                $this->moodleSectionService->creerSection($localSection->course_id, $localSection->name, $localSection->id);
            }
            //$localSections = Section::whereNotIn('id', array_column($sections, 'id'))->delete();

           // Synchronisation des modules depuis le client vers Moodle
            // $localModules = Module::whereNotIn('id', array_column($sections, 'modules.id'))->get();
            // foreach ($localModules as $localModule) {
            //     $this->moodleModuleService->creerModule($localModule->course_id, $localModule->modname, $localModule->name, [
            //         'section' => $localModule->section_id,
            //         'modplural' => $localModule->modplural ?? '', // Ajoutez ce champ
            //         'downloadcontent' => $localModule->downloadcontent ?? '', // Ajoutez ce champ
            //     ]);
            // }
            // $localModules = Module::whereNotIn('id', array_column($sections, 'modules.id'))->delete();


            return redirect()->back()->with('success', 'Synchronisation terminée !');
        } 
        catch (\Exception $e) {
            Log::error('Erreur de synchronisation : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de synchronisation : ' . $e->getMessage());
        }
    }
}
