<?php

namespace App\Http\Controllers;

use App\Services\MoodleUserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;

class MoodleUserController extends Controller
{
    protected $MoodleUserService;

    public function __construct(MoodleUserService $MoodleUserService)
    {
        $this->MoodleUserService = $MoodleUserService;
    }

    public function index(Request $request)
    {
        $criteria = $request->get('criteria', []);
        $users = $this->MoodleUserService->getUsers($criteria);
        return view('moodle.users.index', compact('users')); 
    }

    public function show($id): View
    {
        $user = $this->MoodleUserService->getUserById($id);
        return view('moodle.users.show', compact('user'));
    }

    public function findByEmail(Request $request): View
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = $this->MoodleUserService->getUserByEmail($request->email);
        return view('moodle.users.show', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email'
        ]);

        return response()->json($this->MoodleUserService->createUser($validated));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'firstname' => 'sometimes|string',
            'lastname' => 'sometimes|string',
            'email' => 'sometimes|email'
        ]);

        return response()->json($this->MoodleUserService->updateUser($id, $validated));
    }

    public function destroy($id)
    {
        return response()->json($this->MoodleUserService->deleteUser($id));
    }

}