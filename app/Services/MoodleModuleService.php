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

    /**
     * Lister tous les modules d'un cours
     */
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

    /**
     * Créer un nouveau module
     */
    public function creerModule(int $courseId, string $moduleType, string $name, array $additionalParams = []): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_create_module',
                'coursemodule[course]' => $courseId,
                'coursemodule[module]' => $moduleType,
                'coursemodule[name]' => $name
            ]);

            // Ajouter des paramètres supplémentaires si nécessaire
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

    /**
     * Mettre à jour un module
     */
    public function modifierModule(int $moduleId, array $moduleData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_edit_module',
                'coursemoduleid' => $moduleId
            ]);

            // Ajouter les données de modification
            foreach ($moduleData as $key => $value) {
                $params["coursemodule[$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (modifierModule): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprimer un module
     */
    public function supprimerModule(int $moduleId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_delete_module',
                'coursemoduleid' => $moduleId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (supprimerModule): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les détails d'un module
     */
    public function obtenirDetailsModule(int $courseId, int $moduleId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_module',
                'courseid' => $courseId,
                'id' => $moduleId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (obtenirDetailsModule): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Déplacer un module dans un cours
     */
    public function deplacerModule(int $moduleId, int $sectionId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_move_module',
                'coursemoduleid' => $moduleId,
                'sectionid' => $sectionId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (deplacerModule): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Masquer/Afficher un module
     */
    public function changerVisibiliteModule(int $moduleId, bool $visible): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_edit_module',
                'coursemoduleid' => $moduleId,
                'coursemodule[visible]' => $visible ? 1 : 0
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (changerVisibiliteModule): ' . $e->getMessage());
            throw $e;
        }
    }
}