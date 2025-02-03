<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Course;

class MoodleCourseService
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
     * Get all courses
     */
    public function getAllCourses(): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_courses'
            ]);
            $response = Http::get($this->apiUrl, $params);

            $data = $response->json();

            // Check if the response contains an error
            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (getAllCourses): ' . $data['message']);
                return [];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAllCourses): ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new course
     */
    public function createCourse(Course $course)
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_create_courses',
                'courses' => [
                    [
                        'fullname' => $course->fullname,
                        'shortname' => $course->shortname,
                        'categoryid' => $course->category_id,
                        'summary' => $course->summary,
                        'startdate' => $course->startdate ? strtotime($course->startdate) : null,
                        'enddate' => $course->enddate ? strtotime($course->enddate) : null,
                    ]
                ]
            ]);

            //Add optional parameters if they exist
            $optionalFields = ['summary', 'format', 'showgrades', 'newsitems', 'startdate', 'enddate'];
            foreach ($optionalFields as $field) {
                if (isset($courseData[$field])) {
                    $params["courses[0][$field]"] = $courseData[$field];
                }
            }

            $response = Http::get($this->apiUrl, $params);
            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (createCourse): ' . $data['message']);
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (createCourse): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing course
     */
    public function updateCourse(int $courseId, array $courseData): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_update_courses',
                'courses[0][id]' => $courseId
            ]);

            foreach ($courseData as $key => $value) {
                $params["courses[0][$key]"] = $value;
            }

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (updateCourse): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a course
     */
    public function deleteCourse(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_delete_courses',
                'courseids[0]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (deleteCourse): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get course contents
     */
    public function getCourseContents(int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_contents',
                'courseid' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getCourseContents): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all course categories
     */
    public function getCategories(): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_course_get_categories'
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getCategories): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enroll a user in a course
     */
    public function enrollUser(int $userId, int $courseId, int $roleId = 5): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'enrol_manual_enrol_users',
                'enrolments[0][roleid]' => $roleId,
                'enrolments[0][userid]' => $userId,
                'enrolments[0][courseid]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (enrollUser): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Unenroll a user from a course
     */
    public function unenrollUser(int $userId, int $courseId): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'enrol_manual_unenrol_users',
                'enrolments[0][userid]' => $userId,
                'enrolments[0][courseid]' => $courseId
            ]);

            $response = Http::get($this->apiUrl, $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Moodle API Error (unenrollUser): ' . $e->getMessage());
            throw $e;
        }
    }

    public function isServerAvailable(): bool
    {
        try {
            $response = Http::get($this->apiUrl, [
                'wstoken' => $this->token,
                'wsfunction' => 'core_webservice_get_site_info',
                'moodlewsrestformat' => 'json'
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Moodle API Error (isServerAvailable): ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Moodle API Error (isServerAvailable): ' . $e->getMessage());
            return false;
        }
    }
}
