@extends('layouts.app')

@section('content')

@php
$iconClasses = 'fa-solid fa-chevron-right text-blue-500 border border-blue-500 rounded-full px-2 py-1 text-lg transition duration-150 ease-in-out';
$iconActiveClasses = 'transform rotate-90 text-blue-700';
@endphp

<div class="container mx-auto w-4/5 py-8 bg-white rounded-2xl px-32">
    <!-- Bouton Retour -->
    <div class="p-2">
        <a href="{{ url()->previous() }}" 
           class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>
    
    <h1 class="font-bold mb-6 text-3xl">{{ $course->fullname }}</h1>
    <hr class="mb-4 bg-black h-[2px]">

    <div class="space-y-4 info-panels mb-6">
        <div class="border rounded-lg">    
            <h2 class="px-4 py-1 bg-gray-100 rounded-t-lg">
                <button type="button" class="flex items-center justify-between w-full info-panel-toggle">
                    <span>Details</span>
                    <i class="mr-2 {{ $iconClasses }} info-panel-icon"></i>
                </button>
            </h2>
            <div class="px-4 py-2 info-panel-content hidden">
                <p class="mb-2"><strong>Nom:</strong> {{ $course->shortname }}</p>
                <p class="mb-2"><strong>Description :</strong> {{ $course->summary }}</p>
                <p class="mb-2"><strong>Sections:</strong> {{ $course->numsections }}</p>
                <p class="mb-2"><strong>Date Debut:</strong> {{ $course->startdate->format('d/m/Y') }}</p>
                <p class="mb-2"><strong>Date Fin:</strong> {{ $course->enddate ? $course->enddate->format('d/m/Y') : 'Not Defined' }}</p>
                <p class="mb-2"><strong>Enseigant :</strong> {{ $course->teacher->username ?? 'admin' }}</p>
            </div>
        </div>
    </div>

    @foreach ($course->sections as $section)
    <div class="space-y-4 info-panels mb-6">
        <div class="border rounded-lg">
            <h2 class="px-4 py-2 bg-gray-100 rounded-t-lg">
                <button type="button" class="flex items-center justify-between w-full info-panel-toggle">
                    <span>{{ $section->name }}</span>
                    <i class="mr-2 {{ $iconClasses }} info-panel-icon"></i>
                </button>
            </h2>
            <div class="px-4 py-2 info-panel-content hidden">
                @foreach ($section->modules as $module)
                    <div class="bg-gray-50 rounded-lg my-4  mx-12 px-4 py-2 border-l-4 border-blue-500 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-book-open text-blue-600 mr-2"></i>
                                    {{ $module->name }}
                                </h3>
                                
                                @if ($module->modname == 'resource')
                                    <div class="flex justify-center">
                                        <a href="{{ route('module.download', $module->id) }}" 
                                           target="_blank"
                                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                            <i class="fas fa-file-download mr-2"></i>
                                            Télécharger le PDF
                                        </a>
                                    </div>
                                    
                                @elseif ($module->modname == 'assign')
                                    <div class="space-y-4">
                                        <!-- Devoir à remettre -->
                                        <div class="bg-white p-4 rounded-lg border border-orange-200 shadow-sm">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-tasks text-orange-600 mr-2"></i>
                                                <h4 class="font-semibold text-orange-800">Devoir à remettre</h4>
                                            </div>
                                            <p class="text-gray-700 leading-relaxed">
                                                {{ $module->modplural ?? 'Aucune description disponible' }}
                                            </p>
                                        </div>
                                        
                                        <!-- Consignes -->
                                        <div class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                                            <div class="flex items-center mb-2">
                                                <i class="fas fa-clipboard-list text-green-600 mr-2"></i>
                                                <h4 class="font-semibold text-green-800">Consignes</h4>
                                            </div>
                                            <p class="text-gray-700 leading-relaxed ">
                                                {{ $module->file_path ?? 'Aucune consigne disponible' }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            
            @if(Auth::user()->hasRole('ROLE_TEACHER'))
                <hr class="my-4 border-dashed border-gray-400">
                <div class="mt-4 flex justify-center">
                    <button type="button" onclick="toggleModuleForm('{{ $section->id }}')"
                            class="inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-50 hover:bg-blue-100 
                                    text-blue-600 font-medium rounded-lg transition duration-150 ease-in-out
                                    shadow-md hover:shadow-lg space-x-2">
                        <i class="fas fa-plus h-5 w-5"></i>
                        <span>Créer un Nouveau Module</span>
                    </button>
                </div>

                <div id="module-form-{{ $section->id }}" class="mt-4 p-4 border rounded-lg bg-gray-50 hidden mx-auto w-3/4">
                    <h3 class="text-lg font-medium mb-4 text-center">Nouveau Module</h3>
                    <form action="{{ route('modules.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="name-{{ $section->id }}" class="block text-sm font-medium text-gray-700">Nom du Module</label>
                            <input type="text" name="name" id="name-{{ $section->id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="modname-{{ $section->id }}" class="block text-sm font-medium text-gray-700">Type de Module</label>
                            <select name="modname" id="modname-{{ $section->id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required onchange="updateModplural('{{ $section->id }}')">
                                <option value="resource">Ressource</option>
                                <option value="assign">Devoir</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="file_path-{{ $section->id }}" class="block text-sm font-medium text-gray-700">Téléverser une Ressource</label>
                            <input type="file" name="file_path" id="file_path-{{ $section->id }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <input type="hidden" name="downloadcontent" value="1">
                        <input type="hidden" name="modplural" id="modplural-{{ $section->id }}" value="Files">
                        <input type="hidden" name="section_id" value="{{ $section->id }}">

                        <div class="flex space-x-2 justify-center">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Créer
                            </button>
                            <button type="button" onclick="toggleModuleForm('{{ $section->id }}')" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            @endif 
            </div>
        </div>
    </div>
    @endforeach

    @if(Auth::user()->hasRole('ROLE_TEACHER'))
        <div class="flex justify-center mb-6">
            <hr class="my-4 border-dashed border-gray-400 w-full">
        </div>
        <div class="flex justify-center">
            <button id="add-section-btn" class="inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-50 hover:bg-blue-100 
                text-blue-600 font-medium rounded-lg transition duration-150 ease-in-out
                shadow-md hover:shadow-lg space-x-2">
                <i class="fas fa-plus h-5 w-5"></i>
                <span>Ajouter une Section</span>
            </button>
        </div>

        <div id="add-section-form" class="mt-4 p-4 border rounded-lg bg-gray-50 hidden mx-auto w-3/4">
            <h3 class="text-lg font-medium mb-4 text-center">Nouvelle Section</h3>
            <form action="{{ route('sections.store', ['course' => $course->id]) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom de la section</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                
                <div class="flex space-x-2 justify-center">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Créer
                    </button>
                    <button type="button" onclick="toggleSectionForm()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    @endif
    
    @if(Auth::user()->hasRole('ROLE_STUDENT'))
        <div class="flex justify-center">
            <a href="" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                text-white font-medium rounded-lg transition duration-150 ease-in-out
                shadow-md hover:shadow-lg space-x-2">
                <i class="fas fa-user-plus h-5 w-5"></i>
                <span>S'inscrire à ce cours</span>
            </a>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des panneaux d'information
    var infoPanelToggles = document.querySelectorAll('.info-panel-toggle');
    
    infoPanelToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const panelContent = this.closest('h2').nextElementSibling;
            const icon = this.querySelector('.info-panel-icon');
            
            panelContent.classList.toggle('hidden');
            icon.classList.toggle('{{ $iconActiveClasses }}');
        });
    });

    // Gestion du formulaire d'ajout de section
    var addSectionBtn = document.getElementById('add-section-btn');
    var addSectionForm = document.getElementById('add-section-form');

    if (addSectionBtn) {
        addSectionBtn.addEventListener('click', function() {
            addSectionBtn.classList.add('hidden');
            addSectionForm.classList.remove('hidden');
        });
    }
});

function toggleModuleForm(sectionId) {
    const form = document.getElementById(`module-form-${sectionId}`);
    form.classList.toggle('hidden');
}

function toggleSectionForm() {
    const btn = document.getElementById('add-section-btn');
    const form = document.getElementById('add-section-form');
    
    btn.classList.toggle('hidden');
    form.classList.toggle('hidden');
}

function updateModplural(sectionId) {
    const modname = document.getElementById(`modname-${sectionId}`).value;
    const modplural = document.getElementById(`modplural-${sectionId}`);

    if (modname === 'resource') {
        modplural.value = 'Files';
    } else if (modname === 'assign') {
        modplural.value = 'Assignments';
    }
}
</script>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@endsection