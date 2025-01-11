<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Section;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with('section')->get();
        return view('modules.index', compact('modules'));
    }

    public function create()
    {
        $sections = Section::all();
        return view('modules.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modname' => 'required|string|max:255',
            'modplural' => 'required|string|max:255',
            'downloadcontent' => 'required|boolean',
            'file_path' => 'nullable|string|max:255',
            'section_id' => 'required|exists:sections,id',
        ]);

        Module::create($validated);

        return redirect()->route('modules.index')->with('success', 'Module créé avec succès.');
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

        return redirect()->route('modules.index')->with('success', 'Module mis à jour avec succès.');
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->route('modules.index')->with('success', 'Module supprimé avec succès.');
    }
}
