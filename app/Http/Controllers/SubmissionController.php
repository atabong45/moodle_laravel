<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\SubmissionQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
         // Récupérer toutes les questions liées à la soumission (avec leurs réponses potentielles)
         $submissionQuestions = SubmissionQuestion::where('submission_id', $submission->id)->get();
        return view('submissions.show', compact('submission', 'submissionQuestions'));
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

    public function grade(Request $request, Submission $submission)
{
    // Vérifier si l'utilisateur est un enseignant
    if (!Auth::user()->hasRole('ROLE_TEACHER')) {
        return redirect()->back()->with('error', 'Vous n\'avez pas les autorisations pour noter cette soumission.');
    }

    // Validation des données
    $request->validate([
        'grade' => 'required|integer|min:0|max:20',
        'comment' => 'nullable|string|max:255',
    ]);

    // Enregistrer la note
    $grade = Grade::create([
        'grade' => $request->grade,
        'comment' => $request->comment,
        'submission_id' => $submission->id,
        'teacher_id' => Auth::id(),

    ]);

    $submission->update([
        'status' => Submission::STATUS_CORRECTED,
    ]);

    $submission->save();

    return redirect()->route('submissions.show', $submission->id)->with('success', 'Note attribuée avec succès.');
}

}
