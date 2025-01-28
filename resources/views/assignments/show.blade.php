    @extends('layouts.app')

    @section('content')
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <!-- Titre de l'évaluation -->
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $assignment->name }}</h2>
                    <p class="text-sm text-gray-600">Date limite : {{ \Carbon\Carbon::parse($assignment->duedate)->format('d/m/Y H:i') }}</p>
                </div>

                <!-- Bouton Retour -->
                <div class="p-6">
                    <a href="{{ url()->previous() }}" 
                    class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>

                <!-- Questions -->
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Questions</h3>

                    @if($questions->isEmpty())
                        <p class="text-gray-600">Aucune question pour cette évaluation pour le moment.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($questions as $question)
                                <li class="py-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-gray-800">{{ $loop->iteration }}. {{ $question->content }}</p>
                                            <ul class="mt-2 space-y-1">
                                                @foreach(is_string($question->choices) ? json_decode($question->choices) : $question->choices as $key => $choice)
                                                    @if(auth()->user()->hasRole('ROLE_TEACHER') && $assignment->created_by == auth()->user()->id)
                                                        <li class="{{ $key == $question->correct_choice_id ? 'text-green-600 font-medium' : 'text-gray-600' }}">
                                                            {{ chr(65 + $key) }}. {{ $choice }}
                                                        </li>
                                                    @else
                                                        <li class="text-gray-600">
                                                            {{ chr(65 + $key) }}. {{ $choice }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>

                                        </div>

                                        @if(auth()->user()->hasRole('ROLE_TEACHER') && $assignment->created_by == auth()->user()->id)
                                        <div class="space-x-2">
                                            <a href="{{ route('questions.edit', $question->id) }}" 
                                            class="text-blue-500 hover:text-blue-700">
                                                Modifier
                                            </a>
                                            <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(auth()->user()->hasRole('ROLE_TEACHER') && $assignment->created_by == auth()->user()->id)
                    <!-- Bouton pour modifier -->
                     <hr>
                            <a href="{{ route('assignments.questions.edit', $assignment->id) }}" 
                            class="text-blue-500 hover:text-yellow-700 font-medium mx-2">
                                <i class="fas fa-edit mr-1"></i> Modifier
                            </a>
                        <!-- Bouton pour publier l'évaluation -->
                        @if($assignment->is_published)
                            <form action="{{ route('assignments.togglePublish', $assignment->id) }}" method="POST" class="inline">
                                @csrf    
                                @method('POST')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                    <i class="fas fa-ban mr-1"></i> Dépublier
                                </button>
                            </form>
                        @else
                            <form action="{{ route('assignments.togglePublish', $assignment->id) }}" method="POST" class="inline">
                            @csrf    
                            @method('POST')
                                <button type="submit" class="text-green-500 hover:text-green-700 font-medium">
                                    <i class="fas fa-check mr-1"></i> Publier
                                </button>
                            </form>
                        @endif

                    @elseif(auth()->user()->hasRole('ROLE_STUDENT'))
                        <!-- Bouton pour composer si étudiant -->
                        <a href="{{ route('assignments.compose', $assignment->id) }}" 
                        class="text-green-500 hover:text-green-700 font-medium mx-2">
                            <i class="fas fa-pencil-alt mr-1"></i> Composer
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection
