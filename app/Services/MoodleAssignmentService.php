<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Module;

class MoodleAssignmentService
{
    protected string $apiUrl;
    protected string $token;
    protected array $defaultParams;
    protected string $logFilePath;

    public function __construct()
    {
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
        $this->defaultParams = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];

        $this->logFilePath = storage_path('logs/moodle_actions.txt');
    }

    /**
     * Log an action to the sync file
     */
    public function logAction(string $action, array $data): void
    {
        $logEntry = [
            'timestamp' => now()->toIso8601String(),
            'action' => $action,
            'data' => $data
        ];

        $logLine = json_encode($logEntry) . PHP_EOL;
        file_put_contents($this->logFilePath, $logLine, FILE_APPEND);

        Log::info("Moodle assignment action logged: {$action}");
    }

    /**
     * Récupérer tous les assignments d'un cours
     */
    public function getAssignmentsByCourseId(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_assignments',
                'courseids[0]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['courses']) && !empty($data['courses'])) {
                return $data['courses'][0]['assignments'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getAssignmentsByCourseId): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les détails d'un assignment spécifique
     */
    public function getAssignmentById(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_assignments',
                'assignmentids[0]' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['courses']) && !empty($data['courses']) && 
                isset($data['courses'][0]['assignments']) && !empty($data['courses'][0]['assignments'])) {
                return $data['courses'][0]['assignments'][0];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getAssignmentById): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Synchroniser les assignments avec les modules existants
     */
    public function syncAssignmentsWithModules(int $courseId): void
    {
        try {
            $assignments = $this->getAssignmentsByCourseId($courseId);

            foreach ($assignments as $assignment) {
                // Trouver le module correspondant par le cmid (Course Module ID)
                $module = Module::where('moodle_id', $assignment['cmid'])->first();

                if ($module) {
                    // Nettoyer et préparer les données
                    $intro = $this->cleanHtmlContent($assignment['intro'] ?? '');
                    $activity = $this->cleanHtmlContent($assignment['activity'] ?? '');

                    // Mettre à jour le module avec les informations de l'assignment
                    $module->update([
                        'modplural' => $intro, // Stocker intro dans modplural
                        'file_path' => $activity,    // Stocker activity dans file_path
                    ]);

                    Log::info("Module mis à jour avec les données d'assignment", [
                        'module_id' => $module->id,
                        'assignment_id' => $assignment['id'],
                        'cmid' => $assignment['cmid']
                    ]);

                    $this->logAction('assignment_sync', [
                        'module_id' => $module->id,
                        'assignment_id' => $assignment['id'],
                        'cmid' => $assignment['cmid'],
                        'name' => $assignment['name']
                    ]);
                } else {
                    Log::warning("Aucun module trouvé pour l'assignment", [
                        'assignment_id' => $assignment['id'],
                        'cmid' => $assignment['cmid'],
                        'name' => $assignment['name']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la synchronisation des assignments: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Nettoyer le contenu HTML pour le stockage
     */
    private function cleanHtmlContent(string $content): string
    {
        // Supprimer les balises HTML mais garder le contenu textuel
        $cleanContent = strip_tags($content);
        
        // Remplacer les entités HTML
        $cleanContent = html_entity_decode($cleanContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Nettoyer les espaces en trop
        $cleanContent = trim(preg_replace('/\s+/', ' ', $cleanContent));
        
        return $cleanContent;
    }

    /**
     * Créer un nouvel assignment
     */
    public function createAssignment(int $courseId, array $assignmentData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_save_assignment',
                'assignmentdata' => $assignmentData
            ]);

            $response = Http::post($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (createAssignment): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les soumissions d'un assignment
     */
    public function getAssignmentSubmissions(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_submissions',
                'assignmentids[0]' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (getAssignmentSubmissions): ' . $e->getMessage());
            return [];
        }
    }
}