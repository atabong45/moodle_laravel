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
            Log::info('Action a traitée : ' . $entry['action'] . ' donnee ' . json_encode($entry['data']));
            
            if ($success) {
                $successfulActions[] = $entry;
                Log::info('success');
            } else {
                // Écrire les actions échouées dans le fichier temporaire
                fwrite($tempFile, $line);
                Log::info('echec');
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
                    $course->fullname = $data['fullname'];
                    $course->shortname = $data['shortname'];
                    $course->category_id = $data['category_id'];
                    $course->summary = $data['summary'];
                    $course->numsections = $data['numsections'];
                    $course->startdate = $data['startdate'];
                    $course->enddate = $data['enddate'];
                    Log::info(' creation du cour avec les donnees data ' . json_encode($data));

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
                
                
                    // Gestion des sections
case 'section_create':
    $section = new Section();
    $section->name = $data['name'];
    $section->course_id = $data['course_id'];
    $moodle_id = Course::where('id', $data['course_id'])->value('moodle_id');
    
    // Récupérer le nombre de sections existantes pour déterminer le numéro
    $sectionNumber = 1;
    
    $sectionData = [
        'summary' => $data['summary'] ?? '',
        'visible' => $data['visible'] ?? 1
    ];
    
    $result =$this->moodleSectionService->creerSection(
        $moodle_id ,
        $data['name'],
        $sectionNumber,
        $sectionData
    ) !== null;
        Log::debug("Résultat de création de section: " . json_encode($result));
    
    return !empty($result);
    
case 'section_update':
    $sectionData = [
        'name' => $data['name'],
        'visible' => $data['visible'] ?? 1,
        'summary' => $data['summary'] ?? ''
    ];
    
    return $this->moodleSectionService->modifierSection(
        $data['course_id'],
        $data['id'],
        $sectionData
    ) !== null;
    
case 'section_delete':
    return $this->moodleSectionService->supprimerSection(
        $data['id']
    ) !== null;
                return $this->moodleSectionService->changerVisibiliteSection(
                    $data['course_id'],
                    $data['id'],
                    false
                ) !== null;
                
            
            
            // Gestion des modules
            case 'module_create':
                $moduleData = [
                    'course' => $data['course_id'],
                    'section' => $data['section_id'],
                    'name' => $data['name'],
                    'modname' => $data['modname'],
                    'modplural' => $data['modplural'] ?? $data['modname'] . 's',
                    'downloadcontent' => $data['downloadcontent'] ?? 0,
                ];
                
                // Ajouter des paramètres spécifiques selon le type de module
                if ($data['modname'] === 'resource') {
                    $moduleData['intro'] = $data['summary'] ?? '';
                    $moduleData['files'] = $data['file_path'] ?? '';
                }
                
                return $this->moodleModuleService->creerModule(
                    $data['course_id'],
                    $data['modname'],
                    $data['name'],
                    $moduleData
                ) !== null;
                
            case 'module_update':
                $moduleData = [
                    'name' => $data['name'],
                    'modname' => $data['modname'],
                    'modplural' => $data['modplural'] ?? $data['modname'] . 's',
                    'downloadcontent' => $data['downloadcontent'] ?? 0,
                ];
                
                // Pour les ressources, mettre à jour le fichier si nécessaire
                if ($data['modname'] === 'resource' && isset($data['file_path'])) {
                    $moduleData['files'] = $data['file_path'];
                }
                
            //     return $this->moodleModuleService->modifierModule(
            //         $data['course_id'],
            //         $data['id'],
            //         $moduleData
            //     ) !== null;
                
            // case 'module_delete':
            //     return $this->moodleModuleService->supprimerModule(
            //         $data['course_id'],
            //         $data['id']
            //     ) !== null;
                
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
