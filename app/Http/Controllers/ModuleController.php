<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Services\MoodleModuleService;

class ModuleController extends Controller
{
    protected $moodleModuleService;

    public function __construct(MoodleModuleService $moodleModuleService)
    {
        $this->moodleModuleService = $moodleModuleService;
    }

    public function index()
    {
        $modules = Module::with('section')->get();
        
        return view('modules.index', compact('modules'));
    }

    public function create(Request $request)
    {
        $sectionId = $request->get('section_id');
        $sections = Section::all();
        return view('modules.create', compact('sections', 'sectionId'));
    }

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'modname' => ['required', 'string', 'max:255'],
            'file_path' => ['required', 'file', 'max:10240'], // max 10 Mo
            'section_id' => ['required', 'exists:sections,id'],
            'downloadcontent' => ['required', 'integer', 'in:0,1'],
            'modplural' => ['required', 'string', 'in:Files,Assignments'],
        ]);

        // Stockage simple du fichier
        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('pdfs', 'public');
        }

        // Création du module
        $module = Module::create($validated);

        // Log de création (assure-toi que le service injecté existe bien)
        $this->moodleModuleService->logModuleCreation($module);

        // Vérifie que la relation section existe avant d'accéder à course_id
        if (!$module->relationLoaded('section')) {
            $module->load('section');
        }

        $courseId = optional($module->section)->course_id;

        // Sécurise la redirection même si le cours n'existe pas
        if ($courseId) {
            return redirect()
                ->route('courses.show', ['course' => $courseId])
                ->with('success', 'Module créé avec succès.');
        }

        return redirect()->back()->withErrors('Le cours associé est introuvable.');
    }


    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    public function edit(Module $module)
    {
        $sections = Section::all();
        return view('modules.edit', compact('module', 'sections'));
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modname' => 'required|string|max:255',
            'modplural' => 'required|string|max:255',
            'downloadcontent' => 'required|boolean',
            'file_path' => 'nullable|string|max:255',
            'section_id' => 'required|exists:sections,id',
        ]);

        $module->update($validated);
        $this->moodleModuleService->logModuleUpdate($module);

        return redirect()->route('modules.index')->with('success', 'Module mis à jour avec succès.');
    }

    public function destroy(Module $module)
    {
        $this->moodleModuleService->logModuleDeletion($module);
        $module->delete();

        return redirect()->route('modules.index')->with('success', 'Module supprimé avec succès.');
    }

public function download(Module $module)
{
    // Si c'est un fichier Moodle
    if ($module->moodle_id) {
        return redirect($module->file_path);
    }
    
    // Si c'est un fichier local
    return response()->download(storage_path('app/public/' . $module->file_path));
}
}
