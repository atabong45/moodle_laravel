@extends('layouts.app')

@section('content')

@php
$iconClasses = 'fa-solid fa-chevron-down text-blue-500 border border-blue-500 rounded-full px-2 py-1 text-lg transition duration-150 ease-in-out';
$iconActiveClasses = 'transform rotate-90 text-blue-700';
@endphp

<div class="container mx-auto py-8 bg-white rounded-2xl p-6">
      <!-- Bouton Retour -->
      <div class="p-6">
                    <a href="{{ url()->previous() }}" 
                    class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>
    <h1 class="font-bold mb-6 text-3xl">{{ $course->fullname }}</h1>
    <hr class="mb-4 bg-black h-[2px] ">

    <div class="space-y-4 info-panels mb-6">
        <div class="border rounded-lg">    
            <h2 class="px-4 py-1 bg-gray-100 rounded-t-lg">
                <button type="button" class="flex items-center justify-between w-full info-panel-toggle">
                    <span>Details</span>
                    <i class="mr-2 {{ $iconClasses }} info-panel-icon"></i>
                </button>
            </h2>
            <div class="px-4 py-2 info-panel-content hidden">
                <p class="mb-2"><strong>Short Name:</strong> {{ $course->shortname }}</p>
                <p class="mb-2"><strong>Description :</strong> {{ $course->summary }}</p>
                <p class="mb-2"><strong>Sections:</strong> {{ $course->numsections }}</p>
                <p class="mb-2"><strong>Start Date:</strong> {{ $course->startdate->format('d/m/Y') }}</p>
                <p class="mb-2"><strong>End Date:</strong> {{ $course->enddate ? $course->enddate->format('d/m/Y') : 'Not Defined' }}</p>
                <p class="mb-2"><strong>Teacher:</strong> {{ $course->teacher->username ?? 'admin User' }}</p>
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
                    <p class="mb-2"><strong>Nom du Document:</strong> {{ $module->modname }}</p>
                    <div class="flex justify-center items-center">
                        <a href="{{ $module->file_path }}" target="_blank" rel="noopener noreferrer"
                                {{-- <a href="{{ route('modules.download', $module->id) }}"                                 --}}
                            download
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                                text-white font-medium rounded-lg transition duration-150 ease-in-out
                                shadow-md hover:shadow-lg space-x-2">
                            <i class="fas fa-file-download h-5 w-5"></i>
                            <span>Télécharger le PDF</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach


    @if(Auth::user()->hasRole('ROLE_TEACHER'))
    <a href="javascript:void(0);" id="add-section-btn" class="btn btn-primary text-white bg-blue-500 p-2 rounded">Ajouter une Section</a>
    <a href="{{ route('sections.create', ['course' => $course->id]) }}" id="add-section-btn" class="btn btn-primary text-white bg-blue-500 p-2 rounded">Ajouter une Section</a>
    @endif
    
    @if(Auth::user()->hasRole('ROLE_STUDENT'))
    <a href="" class="bg-blue-500 p-2 rounded text-white"> S'inscrire à ce cours</a>
    @endif

    <div id="add-section-form" class="container" style="display: none;">
        <h1 class=" font-bold text-xl">Créer une nouvelle Section pour {{ $course->fullname }}</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sections.store', ['course' => $course->id]) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nom de la section</label>
                <input type="text" name="name" id="name" class="form-control rounded" required>
            </div>
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <button type="submit" class="btn btn-success bg-blue-500 p-2 text-white rounded">Créer</button>
        </form>
    </div>

    <div class="mt-4">
        <a href="{{ route('courses.index') }}" class="items-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
            <i class="fa-solid fa-arrow-left mr-2"></i> Retour à la page des cours
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var infoPanelToggles = document.querySelectorAll('.info-panel-toggle');
    var infoPanelIcons = document.querySelectorAll('.info-panel-icon');
    var infoPanelContents = document.querySelectorAll('.info-panel-content');

    infoPanelToggles.forEach(function(toggle, index) {
        toggle.addEventListener('click', function() {
            infoPanelContents[index].classList.toggle('hidden');
            infoPanelIcons[index].classList.add('{{ $iconActiveClasses }}');
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var addSectionBtn = document.getElementById('add-section-btn');
    var addSectionForm = document.getElementById('add-section-form');

    addSectionBtn.addEventListener('click', function () {
        addSectionBtn.style.display = 'none';
        addSectionForm.style.display = 'block';
    });

    var accordionButtons = document.querySelectorAll('.accordion-button');

    accordionButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var icon = this.querySelector('i');
            var target = this.getAttribute('data-target');
            var targetElement = document.querySelector(target);

            targetElement.addEventListener('shown.bs.collapse', function () {
                icon.classList.add('{{ $iconActiveClasses }}');
            });

            targetElement.addEventListener('hidden.bs.collapse', function () {
                icon.classList.remove('{{ $iconActiveClasses }}');
            });
        });
    });
});
</script>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@endsection