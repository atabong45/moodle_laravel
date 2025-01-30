<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\MoodleCourseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SynchronisationController extends Controller
{
    protected $moodleCourseService;
    public function __construct(MoodleCourseService $moodleCourseService)
    {
        $this->moodleCourseService = $moodleCourseService;
    }

    public function synchronise(Request $request)
    {
        
    }
}
