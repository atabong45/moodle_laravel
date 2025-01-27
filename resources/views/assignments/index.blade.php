@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Barre de sélection -->
    <div class="text-center mb-4">
        <a href="{{ route('assignments.index', ['view' => 'assignments']) }}" 
           class="btn {{ request('view') === 'assignments' ? 'btn-primary' : 'btn-outline-primary' }}">
            Enoncés
        </a>
        <a href="{{ route('assignments.index', ['view' => 'submissions']) }}" 
           class="btn {{ request('view') === 'submissions' ? 'btn-primary' : 'btn-outline-primary' }}">
            Soumissions
        </a>
    </div>

    @if(request('view') === 'submissions')
        <h3>Liste des Soumissions</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Etudiant</th>
                    <th>Evaluation</th>
                    <th>Statut</th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submissions as $submission)
                    <tr>
                        <td>{{ $submission->id }}</td>
                        <td>{{ $submission->student->name }}</td>
                        <td>{{ $submission->assignment->name }}</td>
                        <td>{{ $submission->status }}</td>
                        <td>{{ $submission->grade->grade ?? 'Non corrigée' }}</td>
                        <td>
                            <a href="{{ route('submissions.show', $submission->id) }}" class="btn btn-info btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <h3>Liste des Enoncés</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Module</th>
                    <th>Date Limite</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->id }}</td>
                        <td>{{ $assignment->name }}</td>
                        <td>{{ $assignment->module->name }}</td>
                        <td>{{ $assignment->duedate }}</td>
                        <td>
                            <a href="{{ route('assignments.show', $assignment->id) }}" class="btn btn-info btn-sm">Voir</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
