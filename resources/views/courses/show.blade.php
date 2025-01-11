@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Détails du cours</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $course->fullname }}</h5>
            <p class="card-text"><strong>Nom court :</strong> {{ $course->shortname }}</p>
            <p class="card-text"><strong>Résumé :</strong> {{ $course->summary }}</p>
            <p class="card-text"><strong>Nombre de sections :</strong> {{ $course->numsections }}</p>
            <p class="card-text"><strong>Date de début :</strong> {{ $course->startdate->format('d/m/Y') }}</p>
            <p class="card-text"><strong>Date de fin :</strong> {{ $course->enddate ? $course->enddate->format('d/m/Y') : 'Non défini' }}</p>
            <p class="card-text"><strong>Enseignant :</strong> {{ $course->teacher->username }}</p>
        </div>
    </div>
    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">Retour à la liste</a>
</div>
@endsection
