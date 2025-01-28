<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Module;
use App\Models\Question;
use App\Models\Submission;
use App\Models\SubmissionQuestion;
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

        public function compose(Assignment $assignment)
        {
            $user = Auth::user();

 
            // Vérifiez si l'évaluation contient des questions
            if (!is_array($assignment->question_ids) || empty($assignment->question_ids)) {
                return redirect()->back()->with('error', 'Cette évaluation ne contient aucune question.');
            }

            // Créez une nouvelle soumission
            $submission = Submission::create([
                'status' => Submission::STATUS_PENDING,
                'file_path' => '',
                'assignment_id' => $assignment->id,
                'student_id' => $user->id,
            ]);

            
            // Créez des questions de soumission
            $submissionQuestions = [];
            $questions = Question::whereIn('id', $assignment->question_ids)->get();

            foreach ($questions as $question) {
                $submissionQuestion = SubmissionQuestion::create([
                    'submission_id' => $submission->id,
                    'content' =>$question->content,
                    'choices' => json_decode($question->choices),
                    'correct_choice_id' => $question->correct_choice_id,
                ]);
                $submissionQuestion;
                $submissionQuestions[] = $submissionQuestion;
            }

            // Passez les questions à la vue
            return view('assignments.compose', compact('assignment', 'submission', 'submissionQuestions'));
        }


        public function submit(Request $request, Assignment $assignment)
        {
            $user = Auth::user();
            $submission = Submission::where('assignment_id', $assignment->id)
                ->where('student_id', $user->id)
                ->firstOrFail();
        
            // Récupérer toutes les questions liées à la soumission (avec leurs réponses potentielles)
            $submissionQuestions = SubmissionQuestion::where('submission_id', $submission->id)->get();
            
            // Valider les réponses soumises par l'élève
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*' => 'integer', // Chaque réponse doit être un ID de choix valide
            ]);
        
            // Persister les réponses de l'élève pour chaque question soumise
            foreach ($submissionQuestions as $submissionQuestion) {
                // Vérifier si l'élève a répondu à cette question
                if (isset($validated['answers'][$submissionQuestion->id])) {
                    $studentAnswer = $validated['answers'][$submissionQuestion->id];
        
                    // Mettez à jour ou créez la réponse de l'élève pour chaque question
                    $submissionQuestion->update([
                        'student_answer_id' => $studentAnswer,  // Persister la réponse de l'élève
                    ]);
                }
            }
        
            // Mettre à jour le statut de la soumission pour indiquer qu'elle a été corrigée
            $submission->update([
                'status' => Submission::STATUS_PENDING,
            ]);
        
            // Rediriger vers la vue des soumissions (par exemple, page de récapitulatif)
            return redirect()->route('assignments.index', ['view' => 'submissions'])  // Remplacez par la route appropriée si nécessaire
                ->with('success', "Vos réponses ont été soumises avec succès.");
        }
        






}
