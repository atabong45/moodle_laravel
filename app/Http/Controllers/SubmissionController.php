<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Module;
use App\Models\Grade;
use App\Services\MoodleSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    protected $submissionService;

    public function __construct(MoodleSubmissionService $submissionService)
    {
        $this->submissionService = $submissionService;
    }

    /**
     * Affiche les soumissions locales pour un devoir.
     * La synchronisation est une tâche de fond, ici on affiche les données locales.
     *
     * @param Module $module
     * @return \Illuminate\View\View
     */
    public function index(Module $module)
    {
        $assignment = $module->assignment;

        if (!$assignment) {
            return redirect()->back()->with('error', 'Aucun devoir trouvé pour ce module.');
        }

        // On récupère les soumissions depuis la base de données LOCALE.
        $submissions = $assignment->submissions()->with(['user', 'grade.teacher'])->get();

        return view('assignment-submissions', compact('module', 'submissions', 'assignment'));
    }

    /**
     * Enregistre une nouvelle soumission dans la base de données locale
     * et la journalise pour une synchronisation future.
     *
     * @param Request $request
     * @param Module $module
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Module $module)
    {
        dd($module);
        $assignment = $module->assignment;

        dd($assignment);
        dd(2);

        $request->validate([
            'content' => 'sometimes|nullable|string|max:65535',
            'file' => 'sometimes|nullable|file|mimes:pdf,txt,zip,doc,docx|max:5120', // 5MB
        ]);

        dd(3);

        if (empty($request->input('content')) && !$request->hasFile('file')) {
            return redirect()->back()->withErrors(['content' => 'Vous devez fournir au moins un texte ou un fichier.']);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            // On stocke le fichier dans un dossier spécifique à l'utilisateur pour l'organisation
            $filePath = $request->file('file')->store('submissions/' . Auth::id());
        }

        // 1. Sauvegarder la soumission dans la base de données locale
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'file_path' => $filePath,
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        dd($submission);

        // 2. Journaliser l'action pour une synchronisation ultérieure
        //    (On suppose que votre service a une méthode pour cela)
        try {
            $this->submissionService->logSubmissionCreation($submission);
        } catch (\Exception $e) {
            Log::error("Échec de la journalisation de la soumission #{$submission->id}: " . $e->getMessage());
            // Même si la journalisation échoue, la soumission est sauvée pour l'utilisateur.
            // On pourrait ajouter une alerte pour l'admin.
        }

        return redirect()->back()->with('success', 'Votre devoir a été enregistré. Il sera synchronisé prochainement.');
    }

    /**
     * Enregistre une note dans la base de données locale
     * et la journalise pour une synchronisation future.
     *
     * @param Request $request
     * @param Submission $submission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function grade(Request $request, Submission $submission)
    {
        // Utiliser une Policy ou un Gate est la meilleure pratique, mais la vérification directe est aussi possible.
        if (!Auth::user()->hasRole('ROLE_TEACHER')) {
            return redirect()->back()->with('error', 'Action non autorisée.');
        }

        $request->validate([
            'grade' => 'required|numeric|min:0|max:20',
            'feedback' => 'nullable|string'
        ]);

        // 1. Sauvegarder la note dans la base de données locale
        $grade = Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'grade' => $request->input('grade'),
                'comment' => $request->input('feedback'),
                'teacher_id' => Auth::id()
            ]
        );

        $submission->update(['status' => 'graded']);

        // 2. Journaliser l'action pour une synchronisation ultérieure
        try {
            $this->submissionService->logGradeUpdate($grade);
        } catch (\Exception $e) {
            Log::error("Échec de la journalisation de la note pour la soumission #{$submission->id}: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'La note a été enregistrée. Elle sera synchronisée prochainement.');
    }
}
