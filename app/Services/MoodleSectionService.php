<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Section;

class MoodleSectionService
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

        Log::info("Moodle section action logged: {$action}");
    }

    /**
     * Log section creation action
     */
    public function logSectionCreation(Section $section): void
    {
        $sectionData = [
            'id' => $section->id,
            'name' => $section->name,
            'course_id' => $section->course_id,
        ];

        $this->logAction('section_create', $sectionData);
    }

    /**
     * Log section update action
     */
    public function logSectionUpdate(Section $section): void
    {
        $sectionData = [
            'id' => $section->id,
            'name' => $section->name,
            'course_id' => $section->course_id,
        ];

        $this->logAction('section_update', $sectionData);
    }

    /**
     * Log section deletion action
     */
    public function logSectionDeletion(Section $section): void
    {
        $this->logAction('section_delete', ['id' => $section->id]);
    }

    public function listerSectionsCours(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_contents',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {

            Log::error('Erreur API Moodle (listerSectionsCours): ' . $e->getMessage(), [
                'file' => storage_path('logs/laravel.log')
            ]);
            Log::error('Erreur API Moodle (listerSectionsCours): ' . $e->getMessage());

            return [];
        }
    }

    public function creerSection(int $courseId, string $name, int $sectionNumber, array $sectionData = []): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_edit_section',
                'courseid' => $courseId,
                'section[name]' => $name,
                'section[sectionnumber]' => $sectionNumber
            ]);

            foreach ($sectionData as $key => $value) {
                $params["section[$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (creerSection): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour une section existante
     * Cette méthode utilise core_course_update_sections pour modifier plusieurs attributs
     * d'une section en une seule requête
     */
    public function modifierSection(int $courseId, int $sectionId, array $sectionData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_update_sections',
                'courseid' => $courseId,
                'sections[0][id]' => $sectionId
            ]);

            // Ajouter les données de modification
            foreach ($sectionData as $key => $value) {
                $params["sections[0][$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (modifierSection): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Changer la visibilité d'une section
     */
    public function changerVisibiliteSection(int $courseId, int $sectionId, bool $visible): array
    {
        try {
            return $this->modifierSection($courseId, $sectionId, [
                'visible' => $visible ? 1 : 0
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (changerVisibiliteSection): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtenir une section spécifique avec ses modules
     * Cette méthode filtre les résultats de get_contents pour retourner
     * uniquement la section demandée
     */
    public function obtenirSection(int $courseId, int $sectionId): ?array
    {
        try {
            $sections = $this->listerSectionsCours($courseId);
            
            foreach ($sections as $section) {
                if ($section['id'] == $sectionId) {
                    return $section;
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (obtenirSection): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Déplacer une section vers une nouvelle position
     */
    public function deplacerSection(int $courseId, int $sectionId, int $position): array
    {
        try {
            return $this->modifierSection($courseId, $sectionId, [
                'sectionnumber' => $position
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (deplacerSection): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mettre à jour le résumé d'une section
     */
    public function modifierResume(int $courseId, int $sectionId, string $summary): array
    {
        try {
            return $this->modifierSection($courseId, $sectionId, [
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (modifierResume): ' . $e->getMessage());
            throw $e;
        }
    }
}