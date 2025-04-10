<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Module;

class MoodleModuleService
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

        $this->logFilePath = storage_path('logs/moodle_module_actions.txt');
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

        Log::info("Moodle module action logged: {$action}");
    }

    /**
     * Log module creation action
     */
    public function logModuleCreation(Module $module): void
    {
        $moduleData = [
            'id' => $module->id,
            'name' => $module->name,
            'modname' => $module->modname,
            'section_id' => $module->section_id,
            'course_id' => $module->course_id,
        ];

        $this->logAction('module_create', $moduleData);
    }

    /**
     * Log module update action
     */
    public function logModuleUpdate(Module $module): void
    {
        $moduleData = [
            'id' => $module->id,
            'name' => $module->name,
            'modname' => $module->modname,
            'section_id' => $module->section_id,
            'course_id' => $module->course_id,
        ];

        $this->logAction('module_update', $moduleData);
    }

    /**
     * Log module deletion action
     */
    public function logModuleDeletion(Module $module): void
    {
        $this->logAction('module_delete', ['id' => $module->id]);
    }

    public function listerModulesCours(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_contents',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (listerModulesCours): ' . $e->getMessage());
            return [];
        }
    }

    public function creerModule(int $courseId, string $moduleType, string $name, array $additionalParams = []): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_create_module',
                'coursemodule[course]' => $courseId,
                'coursemodule[module]' => $moduleType,
                'coursemodule[name]' => $name
            ]);

            foreach ($additionalParams as $key => $value) {
                $params["coursemodule[$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (creerModule): ' . $e->getMessage());
            throw $e;
        }
    }
}