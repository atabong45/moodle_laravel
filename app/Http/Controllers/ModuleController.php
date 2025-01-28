<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

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

public function download(Module $module)
{
    // URL Moodle
    $url = $module->file_url; // Supposons que l'URL est stockée dans ce champ

    // Extraire le contextid, le composant et le nom du fichier depuis l'URL
    preg_match('/pluginfile.php\/(\d+)\/([\w_]+)\/content\/\d+\/(.+)$/', $url, $matches);

    if (count($matches) < 4) {
        return response()->json(['error' => 'URL Moodle invalide'], 400);
    }

    $contextId = $matches[1];
    $component = $matches[2];
    $filename = urldecode($matches[3]);

    // Requête pour trouver le contenthash du fichier
    $fileInfo = DB::table('mdl_files')
        ->where('component', $component)
        ->where('contextid', $contextId)
        ->where('filename', $filename)
        ->first(['contenthash']);

    if (!$fileInfo) {
        return response()->json(['error' => 'Fichier introuvable dans Moodle'], 404);
    }

    $contentHash = $fileInfo->contenthash;

    // Récupérer le chemin MoodleData depuis le fichier de configuration
    $moodleDataPath = config('moodle.dataroot'); // Assurez-vous que ce paramètre est défini

    // Construire le chemin complet vers le fichier
    $fileDir = substr($contentHash, 0, 2) . '/' . substr($contentHash, 2, 2);
    $filePath = $moodleDataPath . '/filedir/' . $fileDir . '/' . $contentHash;

    if (!file_exists($filePath)) {
        return response()->json(['error' => 'Fichier physique introuvable'], 404);
    }

    // Nom du fichier à télécharger
    $fileName = basename($filename);

    // Chemin de destination dans le répertoire de téléchargement
    $destinationPath = storage_path('app/downloads/' . $fileName);

    // Créer le répertoire de téléchargement si nécessaire
    if (!Storage::exists('downloads')) {
        Storage::makeDirectory('downloads');
    }

    // Copier le fichier dans le répertoire de téléchargement
    Storage::copy($filePath, 'downloads/' . $fileName);

    // Retourner le fichier en réponse pour le téléchargement
    return Response::download($destinationPath, $fileName);
}
}
