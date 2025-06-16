@extends('layouts.app')

@section('content')

<div class="container mx-auto w-4/5 py-8 bg-white rounded-2xl px-8">
    <!-- Bouton Retour -->
    <div class="p-2">
        <a href="{{ url()->previous() }}" 
           class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>
    
    <!-- En-tête -->
    <div class="flex items-center mb-6">
        <div class="bg-purple-100 p-3 rounded-full mr-4">
            <i class="fas fa-clipboard-list text-purple-600 text-2xl"></i>
        </div>
        <div>
            <h1 class="font-bold text-3xl text-gray-800">{{ $module->name }}</h1>
            <p class="text-gray-600 mt-1">Gestion des soumissions</p>
        </div>
    </div>
    
    <hr class="mb-6 bg-gray-300 h-[1px]">

    <!-- Informations du devoir -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6 border border-blue-200">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 mb-1">Date d'ouverture</p>
                <p class="font-semibold text-gray-800">
                    <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                    @if($module->allowsubmissionsfromdate)
                        {{ $module->allowsubmissionsfromdate->format('l, d F Y, h:i A') }}
                    @else
                        Non défini
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Date limite</p>
                <p class="font-semibold text-gray-800">
                    <i class="fas fa-clock text-orange-600 mr-2"></i>
                    @if($module->duedate)
                        {{ $module->duedate->format('l, d F Y, h:i A') }}
                    @else
                        Non défini
                    @endif
                </p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-blue-200">
            <p class="text-sm text-gray-600 mb-1">Description du devoir</p>
            <p class="text-gray-800">{!! $module->intro ?? 'Aucune description disponible' !!}</p>
        </div>
    </div>

    <!-- Résumé de notation -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                Résumé de notation
            </h2>
        </div>
        
        <div class="p-6">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $submissions->count() }}</p>
                    <p class="text-sm text-gray-600">Participants</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-paper-plane text-green-600 text-2xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $submissions->where('status', 'submitted')->count() }}</p>
                    <p class="text-sm text-gray-600">Soumis</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-orange-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-2xl"></i>
                    </div>
                   <p class="text-2xl font-bold text-gray-800">
                        {{ $submissions->where('status', 'submitted')->whereNull('grade_id')->count() }}
                    </p>
                    <p class="text-sm text-gray-600">À corriger</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-purple-600 text-2xl"></i>
                    </div>
                    @if($module->duedate)
                        <p class="text-2xl font-bold text-gray-800">{{ now()->diffInDays($module->duedate) }} jours</p>
                    @else
                        <p class="text-2xl font-bold text-gray-800">∞</p>
                    @endif
                    <p class="text-sm text-gray-600">Temps restant</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des soumissions -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list text-blue-600 mr-3"></i>
                Soumissions des étudiants
            </h2>
        </div>
        
        @if($submissions->isEmpty())
            <div class="p-8">
                <div class="text-center py-12">
                    <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-inbox text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-3">Aucune soumission disponible</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Les étudiants n'ont pas encore soumis leurs devoirs. Les soumissions apparaîtront ici une fois qu'elles seront reçues.
                    </p>
                </div>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($submissions as $submission)
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 rounded-full w-10 h-10 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $submission->user->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        Soumis le {{ $submission->submitted_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="{{ $submission->status === 'submitted' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ $submission->status === 'submitted' ? 'Soumis' : 'Brouillon' }}
                                </span>
                                <a href="{{ route('submissions.show', $submission->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-eye mr-1"></i> Voir
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection