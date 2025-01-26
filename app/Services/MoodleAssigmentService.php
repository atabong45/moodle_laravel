<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleAssignmentService
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
     * Get all assignments for a specific course
     */
    public function getAssignmentsInCourse(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_assignments',
                'courseids[0]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            // Check for API errors
            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (getAssignmentsInCourse): ' . $data['message']);
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAssignmentsInCourse): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get detailed information about a specific assignment
     */
    public function getAssignmentDetails(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_assignment',
                'assignid' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAssignmentDetails): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get submissions for a specific assignment
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
            Log::error('Moodle API Error (getAssignmentSubmissions): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Submit an assignment for a user
     */
    public function submitAssignment(int $assignmentId, int $userId, array $submissionData = []): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_submit_for_grading',
                'assignmentid' => $assignmentId,
                'userid' => $userId,
                'submissionstatement' => 1
            ]);

            // Add optional submission data
            foreach ($submissionData as $key => $value) {
                $params[$key] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (submitAssignment): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Grade an assignment submission
     */
    public function gradeAssignmentSubmission(int $assignmentId, int $userId, float $grade, string $feedback = ''): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_save_grade',
                'assignmentid' => $assignmentId,
                'userid' => $userId,
                'grade' => $grade,
                'attemptnumber' => -1 // Most recent attempt
            ]);

            // Add optional feedback
            if (!empty($feedback)) {
                $params['feedbacktext'] = $feedback;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (gradeAssignmentSubmission): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get assignment participants (students)
     */
    public function getAssignmentParticipants(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_list_participants',
                'assignid' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAssignmentParticipants): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get assignment grading status
     */
    public function getAssignmentGradingStatus(int $assignmentId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'mod_assign_get_grades',
                'assignmentids[0]' => $assignmentId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAssignmentGradingStatus): ' . $e->getMessage());
            throw $e;
        }
    }
}