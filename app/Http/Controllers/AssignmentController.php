<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Module;
use App\Models\Question;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with('module')->get();
        $submissions = Submission::with(['assignment', 'student', 'grade'])->get();
        return view('assignments.index', compact('assignments', 'submissions'));
    }

    public function create()
        {
            $modules = Module::all();
            $questions = Question::all(); // Récupère toutes les questions existantes
            return view('assignments.create', compact('modules', 'questions'));
        }

    public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'duedate' => 'required|date',
                'attemptnumber' => 'required|integer|min:1',
                'module_id' => 'required|exists:modules,id',
                'questions' => 'nullable|array', // Liste des questions sélectionnées
                'questions.*' => 'exists:questions,id', // Valide que chaque question existe
            ]);

            // Ajouter l'ID de l'utilisateur connecté à l'évaluation
            $assignmentData = $request->only(['name', 'duedate', 'attemptnumber', 'module_id']);
            $assignmentData['created_by'] = Auth::user()->id; // Associe l'utilisateur connecté

            // Crée l'évaluation
            $assignment = Assignment::create($request->only(['name', 'duedate', 'attemptnumber', 'module_id']));

            // Associe les questions à l'évaluation
            if ($request->has('questions')) {
                $assignment->question_ids = $request->questions; // Stocke les IDs des questions dans l'évaluation
                $assignment->save();
            }

            return redirect()->route('assignments.index')->with('success', 'Assignment created successfully.');
        }


    public function show(Assignment $assignment)
    {
        // Charger les questions liées à l'évaluation
        $questions = Question::whereIn('id', $assignment->question_ids ?? [])->get();

        return view('assignments.show', compact('assignment', 'questions'));
    }

    public function edit(Assignment $assignment)
    {
        $modules = Module::all();
        return view('assignments.edit', compact('assignment', 'modules'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duedate' => 'required|date',
            'attemptnumber' => 'required|integer|min:1',
            'module_id' => 'required|exists:modules,id',
        ]);

        $assignment->update($request->all());
        return redirect()->route('assignments.index')->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('assignments.index')->with('success', 'Assignment deleted successfully.');
    }

 

    public function togglePublish(Assignment $assignment)
    {
        if(!$assignment->published){
            if (!$assignment->question_ids || count($assignment->question_ids) === 0) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas publier une évaluation sans questions.');
            }
        }

        // Inverser le statut de publication
        $assignment->published = !$assignment->published;
        $assignment->save();

        $status = $assignment->published ? 'publiée' : 'dépubliée';

        return redirect()->route('assignments.show', $assignment->id)
                        ->with('success', "L'évaluation a été $status avec succès.");
    }

    public function compose(Assignment $assignment)
    {
        // Vérifiez que l'utilisateur a les droits nécessaires pour composer
        if (!!Auth::user()->hasRole('ROLE_STUDENT')) {
            abort(403, 'Vous n\'êtes pas autorisé à composer cette évaluation.');
        }

        // Logique pour afficher la page de composition
        return view('assignments.compose', compact('assignment'));
    }

    // Afficher le formulaire pour modifier toutes les questions d'un assignment
    public function editQuestions(Assignment $assignment)
    {
        // Récupérer les questions liées à l'assignement
        $questions = Question::whereIn('id', $assignment->question_ids ?? [])->get();
        
        return view('assignments.edit_questions', compact('assignment', 'questions'));
    }

    // Mettre à jour toutes les questions d'un assignment
    public function updateQuestions(Request $request, $assignmentId)
{
    // Validation des données
    $request->validate([
        'questions.*.content' => 'required|string',
        'questions.*.choices' => 'required|array|min:2',
        'questions.*.correct_choice_id' => 'required|integer',
        'questions.*.id' => 'required|exists:questions,id',  // Assurez-vous que l'id existe dans la base
    ]);

    // Récupérer l'évaluation
    $assignment = Assignment::findOrFail($assignmentId);

    // Mettre à jour les questions
    foreach ($request->input('questions') as $questionData) {
        // Trouver la question par son ID
        $question = Question::findOrFail($questionData['id']);
        $question->update([
            'content' => $questionData['content'],
            'choices' => json_encode($questionData['choices']),  // Assurez-vous de sérialiser les choix si nécessaire
            'correct_choice_id' => $questionData['correct_choice_id'],
        ]);
    }

    // Rediriger vers la page d'affichage de l'évaluation avec un message de succès
    return redirect()->route('assignments.show', $assignment->id)
                     ->with('success', 'Les questions de l\'évaluation ont été mises à jour.');
}





}
