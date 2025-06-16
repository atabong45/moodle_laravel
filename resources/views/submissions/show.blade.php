@extends('layouts.app')

@section('title', 'Soumission de ' . $submission->user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

    <!-- Colonne Principale : Contenu de la soumission -->
    <div class="lg:col-span-2 space-y-8">
        <header>
            <a href="{{ route('assignments.submissions', $submission->assignment->module->id) }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-2 mb-4">
                <i class="fas fa-arrow-left"></i>
                Retour à toutes les soumissions
            </a>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">
                Soumission de <span class="text-indigo-600">{{ $submission->user->name }}</span>
            </h1>
            <p class="mt-2 text-lg text-gray-600">
                Pour le devoir : <span class="font-semibold">{{ $submission->assignment->module->name }}</span>
            </p>
        </header>

        <!-- Fichier Soumis -->
        @if ($submission->file_path)
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Fichier joint</h2>
            <div class="flex items-center justify-between gap-4 bg-gray-50 p-4 rounded-lg">
                <a href="{{ route('submissions.download', $submission) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-md">
                    <i class="fas fa-download"></i>
                    <span>Télécharger</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Réponse Texte -->
        @if ($submission->content)
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Réponse Texte</h2>
            <div class="prose prose-lg max-w-none text-gray-700 p-4 bg-gray-50 rounded-lg">
                {!! nl2br(e($submission->content)) !!}
            </div>
        </div>
        @else
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
             <p class="text-gray-500 italic">Aucune réponse texte n'a été fournie.</p>
        </div>
        @endif
    </div>

    <!-- Colonne Latérale : Notation et Infos -->
    <aside class="lg:col-span-1 space-y-6 sticky top-8">
        <!-- Formulaire de Notation pour l'enseignant -->
        @can('grade', $submission)
        <div class="bg-white p-6 rounded-2xl shadow-xl border-2 border-indigo-200">
            <form action="{{ route('submissions.grade', $submission) }}" method="POST">
                @csrf
                <h2 class="text-xl font-bold text-gray-900 mb-1">Noter ce travail</h2>
                <p class="text-sm text-gray-500 mb-6">La note et le commentaire seront visibles par l'étudiant.</p>

                <div class="mb-4">
                    <label for="grade" class="block text-sm font-medium text-gray-700">Note (/20)</label>
                    <input type="number" step="0.5" min="0" max="20" name="grade" id="grade" value="{{ old('grade', $submission->grade->grade ?? '') }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 transition">
                    @error('grade') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label for="feedback" class="block text-sm font-medium text-gray-700">Commentaire / Feedback</label>
                    <textarea name="feedback" id="feedback" rows="6" placeholder="Ajoutez vos commentaires ici..."
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 transition">{{ old('feedback', $submission->grade->comment ?? '') }}</textarea>
                    @error('feedback') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full flex justify-center items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-md">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ $submission->grade ? 'Mettre à jour la note' : 'Enregistrer la note' }}</span>
                </button>
            </form>
        </div>
        @endcan

        <!-- Infos de la soumission -->
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
             <h3 class="text-lg font-bold text-gray-900 mb-4">Détails</h3>
             <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Statut</dt><dd class="font-semibold px-2 py-0.5 rounded-full {{ $submission->status === 'graded' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">{{ ucfirst($submission->status) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Soumis le</dt><dd class="font-semibold">{{ $submission->submitted_at->format('d/m/Y à H:i') }}</dd></div>
                @if($submission->grade)
                <div class="flex justify-between"><dt class="text-gray-500">Noté le</dt><dd class="font-semibold">{{ $submission->grade->updated_at->format('d/m/Y') }}</dd></div>
                @endif
             </dl>
        </div>
    </aside>

</div>
@endsection