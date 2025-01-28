@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">{{ $assignment->name }}</h1>
    <p class="mb-4">Date limite : {{ \Carbon\Carbon::parse($assignment->duedate)->format('d/m/Y H:i') }}</p>

    <form action="{{ route('assignments.submit', $assignment->id) }}" method="POST">
        @csrf

        <h2 class="text-lg font-semibold mb-4">Questions</h2>
        @if($submissionQuestions && count($submissionQuestions) > 0)
            @foreach ($submissionQuestions as $question)
                <div class="mb-6">
                    <h3 class="text-sm font-medium">{{ $loop->iteration }}. {{ $question->content }}</h3>
                    <div class="mt-2">
                        @foreach($question->choices as $key => $choice) <!-- Plus besoin de json_decode() ici -->
                            <div class="flex items-center mb-2">
                                <input type="radio" id="question_{{ $question->id }}_choice_{{ $key }}" name="answers[{{ $question->id }}]" value="{{ $key }}" class="form-radio">
                                <label for="question_{{ $question->id }}_choice_{{ $key }}" class="ml-2">{{ chr(65 + $key) }}. {{ $choice }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <p>Aucune question disponible pour cette soumission.</p>
        @endif

        <button type="submit" class="btn btn-primary">Soumettre</button>
    </form>
</div>
@endsection
