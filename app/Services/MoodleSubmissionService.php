<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class MoodleSubmissionService
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
     * Récupérer les soumissions d'un devoir
     */
    public function listerSoumissions(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_submissions',
                'assignmentids[0]' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (listerSoumissions): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Soumettre un devoir
     */
    public function soumettre(int $assignmentId, int $userId, ?UploadedFile $fichier = null): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_submit_for_grading',
                'assignmentid' => $assignmentId,
                'userid' => $userId
            ]);

            // Gestion du fichier si présent
            if ($fichier) {
                $params['files[0]'] = base64_encode(file_get_contents($fichier->getPathname()));
                $params['files[0]_name'] = $fichier->getClientOriginalName();
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (soumettre): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Évaluer une soumission
     */
    public function evaluer(int $assignmentId, int $userId, float $note, ?string $commentaire = null): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_save_grade',
                'assignmentid' => $assignmentId,
                'userid' => $userId,
                'grade' => $note
            ]);

            // Ajout du commentaire si présent
            if ($commentaire) {
                $params['feedbacktext'] = $commentaire;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (evaluer): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les détails d'une soumission
     */
    public function obtenirDetails(int $assignmentId, int $userId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_submission_details',
                'assignmentid' => $assignmentId,
                'userid' => $userId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (obtenirDetails): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ajouter un fichier à une soumission
     */
    public function ajouterFichier(int $assignmentId, int $userId, UploadedFile $fichier): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_save_submission',
                'assignmentid' => $assignmentId,
                'userid' => $userId,
                'files[0]' => base64_encode(file_get_contents($fichier->getPathname())),
                'files[0]_name' => $fichier->getClientOriginalName()
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (ajouterFichier): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Récupérer les soumissions non notées
     */
    public function listerSoumissionsNonNotees(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_list_ungraded_submissions',
                'assignmentid' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erreur API Moodle (listerSoumissionsNonNotees): ' . $e->getMessage());
            return [];
        }
    }
}