<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Module;
use App\Models\Submission;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
{
    $assignments = Assignment::with('module')->get(); // Inclure les modules associés pour chaque évaluation
    $submissions = Submission::with(['assignment', 'student', 'grade'])->get(); // Inclure les relations nécessaires
    return view('assignments.index', compact('assignments', 'submissions'));
}


    public function create()
    {
        $modules = Module::all();
        return view('assignments.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duedate' => 'required|date',
            'attemptnumber' => 'required|integer|min:1',
            'module_id' => 'required|exists:modules,id',
        ]);

        Assignment::create($request->all());
        return redirect()->route('assignments.index')->with('success', 'Assignment created successfully.');
    }

    public function show(Assignment $assignment)
    {
        return view('assignments.show', compact('assignment'));
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
}
