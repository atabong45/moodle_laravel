<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['assignment', 'student'])->get();
        return view('submissions.index', compact('submissions'));
    }

    public function create()
    {
        $assignments = Assignment::all();
        $students = User::where('role', 'student')->get();
        return view('submissions.create', compact('assignments', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|string',
            'file_path' => 'required|string',
            'assignment_id' => 'required|exists:assignments,id',
            'student_id' => 'required|exists:users,id',
        ]);

        Submission::create($request->all());
        return redirect()->route('submissions.index')->with('success', 'Submission created successfully.');
    }

    public function show(Submission $submission)
    {
        return view('submissions.show', compact('submission'));
    }

    public function edit(Submission $submission)
    {
        $assignments = Assignment::all();
        $students = User::where('role', 'student')->get();
        return view('submissions.edit', compact('submission', 'assignments', 'students'));
    }

    public function update(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|string',
            'file_path' => 'required|string',
            'assignment_id' => 'required|exists:assignments,id',
            'student_id' => 'required|exists:users,id',
        ]);

        $submission->update($request->all());
        return redirect()->route('submissions.index')->with('success', 'Submission updated successfully.');
    }

    public function destroy(Submission $submission)
    {
        $submission->delete();
        return redirect()->route('submissions.index')->with('success', 'Submission deleted successfully.');
    }
}
