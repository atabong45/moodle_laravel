<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;

class MoodleEventService
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

    public function getAllEvents(): array
    {
        try {
            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_calendar_get_calendar_events'
            ]);
            $response = Http::get($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (getAllEvents): ' . $data['message']);
                return [];
            }
            return $data;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (getAllEvents): ' . $e->getMessage());
            return [];
        }
    }

    public function createEvent(Event $event)
    {
        Log::error('test');
        try {
            $type = '';

            switch($event->type) {
                case 'utilisateur' : $type = 'user'; break;
                case 'cours' : $type = 'course'; break;
                case 'categorie' : $type = 'category'; break;
                default : $type = 'site';
            }

            $params = array_merge($this->defaultParams, [
                'wsfunction' => 'core_calendar_create_calendar_events',
                'events' => json_encode([
                    [
                        'name' => $event->title,
                        'timestart' => strtotime($event->date),
                        'eventtype' => $type,
                        'courseid' => $event->course_id ?? 0,
                        'categoryid' => $event->category_id ?? 0
                    ]
                ])
            ]);

            $response = Http::asForm()->post($this->apiUrl, $params);
            $data = $response->json();

            if (isset($data['errorcode']) || isset($data['exception'])) {
                Log::error('Moodle API Error (createEvent): ' . $data['message']);
                return false;
            }

            Log::error('Great');

            return true;
        } catch (\Exception $e) {
            Log::error('Moodle API Error (createEvent): ' . $e->getMessage());
            return false;
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
