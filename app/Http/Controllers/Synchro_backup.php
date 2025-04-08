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
use Illuminate\Support\Facades\Storage;

class SynchronisationController extends Controller
{
    protected $moodleCourseService;
    protected $moodleUserService;
    protected $moodleCategoryService;
    protected $moodleSectionService;
    protected $moodleModuleService;
    protected $logFilePath;

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
        $this->logFilePath = storage_path('logs/moodle_actions.txt');
    }

    public function synchronize(Request $request)
    {
        try {
            // Vérifier la disponibilité du serveur Moodle
            $this->checkServerAvailability();

            // Exécuter les actions à partir du fichier de log
            $this->processLoggedActions();
        
            // Synchronisation des catégories
            $moodleCategories = $this->moodleCategoryService->getToutesCategories();
            $moodleCategoryIds = array_column($moodleCategories, 'id');
            foreach ($moodleCategories as $moodleCategory) {
                Category::updateOrCreate(
                    ['moodle_id' => $moodleCategory['id']],
                    [
                        'moodle_id' => $moodleCategory['id'],
                        'name' => $moodleCategory['name'],
                    ]
                );
            }

            // Synchronisation des cours
            $moodleCourses = $this->moodleCourseService->getAllCourses();
            $moodleCourseIds = array_column($moodleCourses, 'id');
            foreach (array_slice($moodleCourses, 1) as $moodleCourse) {
                 // Récupérer l'ID de la catégorie locale correspondant au moodle_id de la catégorie Moodle
                $categoryId = Category::where('moodle_id', $moodleCourse['categoryid'])->value('id');

                // Vérifiez que la catégorie existe avant de mettre à jour le cours
                if (in_array($moodleCourse['categoryid'], $moodleCategoryIds)) {
                    Course::updateOrCreate(
                        ['moodle_id' => $moodleCourse['id']],
                        [
                            'moodle_id' => $moodleCourse['id'],
                            'fullname' => $moodleCourse['fullname'],
                            'shortname' => $moodleCourse['shortname'],
                            'summary' => $moodleCourse['summary'],
                            'numsections' => $moodleCourse['numsections'],
                            'startdate' => $moodleCourse['startdate'] == 0 ? null : $moodleCourse['startdate'],
                            'enddate' => $moodleCourse['enddate'] == 0 ? null : $moodleCourse['enddate'],
                            'teacher_id' => $moodleCourse['teacher_id'] ?? null,
                            'category_id' => $categoryId,
                            'image' => $moodleCourse['image'] ?? null,
                        ]
                    );
                } else {
                    Log::warning('La catégorie avec l\'ID ' . $moodleCourse['categoryid'] . ' n\'existe pas.');
                }
            }

            //Synchronisation des utilisateurs
            $moodleUsers = $this->moodleUserService->getUsers();
            $moodleUserIds = array_column($moodleUsers['users'], 'id');
            foreach ($moodleUsers['users'] as $moodleUser) {
                User::updateOrCreate(
                    ['moodle_id' => $moodleUser['id']],
                    [
                        'name' => $moodleUser['fullname'],
                        'email' => $moodleUser['email'],
                        'moodle_id' => $moodleUser['id'],
                        'password' => bcrypt('defaultpassword'), // Vous pouvez gérer les mots de passe différemment
                        'profile_picture' => $moodleUser['profileimageurl'] ?? null,
                    ]
                );
            }

           // Synchronisation des sections depuis Moodle vers le client
            $moodleCourses = $this->moodleCourseService->getAllCourses();
            foreach (array_slice($moodleCourses, 1)  as $moodleCourse) {
                $sections = $this->moodleSectionService->listerSectionsCours($moodleCourse['id']);
                
                foreach ($sections as $section) {
                    // Récupérer l'ID du cours local correspondant au moodle_id du cours Moodle
                    $courseId = Course::where('moodle_id', $moodleCourse['id'])->value('id');

                    Section::updateOrCreate(
                        ['moodle_id' => $section['id']],
                        [
                            'moodle_id' => $section['id'],
                            'name' => $section['name'],
                            'course_id' => $courseId,
                        ]
                    );
                    // Synchronisation des modules depuis Moodle vers le client
                    foreach ($section['modules'] as $module) {
                        // Récupérer l'ID de la section locale correspondant au moodle_id de la section Moodle
                        $sectionId = Section::where('moodle_id', $section['id'])->value('id');

                        // Vérifiez que la section existe avant de mettre à jour ou créer le module
                        if ($sectionId) {
                            Module::updateOrCreate(
                                ['moodle_id'=>$module['id']],
                                [
                                    'moodle_id'=>$module['id'],
                                    'name' => $module['name'],
                                    'modname' => $module['modname'],
                                    'modplural' => $module['modplural'] ?? '', 
                                    'downloadcontent' => $module['downloadcontent'] ?? '', 
                                    'file_path' => isset($module['contents'][0]['fileurl']) 
                                                        ? str_replace('?forcedownload=1', '?token=' . config('moodle.api_token'), $module['contents'][0]['fileurl']) 
                                                        : '',
                                    'section_id' => $sectionId,
                                    'course_id' => $courseId,
                                ]
                            );
                        } else {
                            Log::warning('La section avec l\'ID ' . $section['id'] . ' n\'existe pas.');
                        }
                    }
                }
            }
           
            return redirect()->back()->with('success', 'Synchronisation terminée !');
        } 
        catch (\Exception $e) {
            Log::error('Erreur de synchronisation : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de synchronisation : ' . $e->getMessage());
        }
    }
    /**
     * Process actions logged in the file
     */
    protected function processLoggedActions()
    {
        if (!file_exists($this->logFilePath)) {
            Log::info('Aucun fichier de log à traiter');
            return;
        }

        // Lire le fichier ligne par ligne
        $file = fopen($this->logFilePath, 'r');
        $tempFilePath = $this->logFilePath . '.temp';
        $tempFile = fopen($tempFilePath, 'w');
        
        $successfulActions = [];
        
        while (($line = fgets($file)) !== false) {
            $entry = json_decode($line, true);
            
            if (!$entry) {
                continue;
            }
            
            $success = $this->executeAction($entry['action'], $entry['data']);
            
            if ($success) {
                $successfulActions[] = $entry;
            } else {
                // Écrire les actions échouées dans le fichier temporaire
                fwrite($tempFile, $line);
            }
        }
        
        fclose($file);
        fclose($tempFile);
        
        // Remplacer l'ancien fichier par le nouveau
        rename($tempFilePath, $this->logFilePath);
        
        Log::info('Actions traitées : ' . count($successfulActions) . ' réussies');
    }

    /**
     * Execute a specific action
     */
    protected function executeAction(string $action, array $data): bool
    {
        try {
            switch ($action) {
                case 'course_create':
                    $course = new Course();
                    $course->id = $data['id'];
                    $course->fullname = $data['fullname'];
                    $course->shortname = $data['shortname'];
                    $course->category_id = $data['category_id'];
                    $course->summary = $data['summary'];
                    $course->numsections = $data['numsections'];
                    $course->startdate = $data['startdate'];
                    $course->enddate = $data['enddate'];
                    
                    return $this->moodleCourseService->createCourse($course);
                    
                case 'course_update':
                    $courseData = [
                        'fullname' => $data['fullname'],
                        'shortname' => $data['shortname'],
                        'summary' => $data['summary'],
                        'numsections' => $data['numsections'],
                        'startdate' => strtotime($data['startdate']),
                        'enddate' => $data['enddate'] ? strtotime($data['enddate']) : null,
                    ];
                    
                    $this->moodleCourseService->updateCourse($data['id'], $courseData);
                    return true;
                    
                case 'course_delete':
                    $this->moodleCourseService->deleteCourse($data['id']);
                    return true;
                    
                default:
                    Log::warning("Action inconnue : {$action}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'exécution de l'action {$action} : " . $e->getMessage());
            return false;
        }
    }

    protected function checkServerAvailability() {        
        // Vérifier la disponibilité du serveur Moodle
        if (!$this->moodleCourseService->isServerAvailable()) {
            return redirect()->back()->with('alert', 'Le serveur Moodle n\'est pas disponible.');
        }
    }


}
