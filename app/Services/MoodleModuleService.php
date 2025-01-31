<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleModuleService
{
    protected string $apiUrl;
    protected string $token;
    protected array $defaultParams;

    public function __construct()
    {
        $this->apiUrl = config('moodle.api_url');
        $this->token = config('moodle.api_token');
        $this->defaultParams = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];
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