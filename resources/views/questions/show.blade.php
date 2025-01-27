@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Détails de la Question</h3>
                    <p class="text-sm text-gray-800"><strong>Contenu :</strong> {{ $question->content }}</p>

                    <div class="mt-4">
                        <strong>Propositions :</strong>
                        <ul class="list-disc pl-6">
                            @foreach ($question->choices as $choice)
                                <li class="text-sm text-gray-800">{{ $choice }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="mt-4 text-sm text-gray-800"><strong>Proposition correcte :</strong> {{ $question->choices[$question->correct_choice_id] }}</p>

                    <div class="mt-6">
                        <a href="{{ route('questions.index') }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                            Retour à la liste des questions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
