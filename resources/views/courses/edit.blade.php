@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier un cours</h1>
    <form action="{{ route('courses.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="fullname" class="form-label">Nom complet</label>
            <input type="text" class="form-control" id="fullname" name="fullname" value="{{ $course->fullname }}" required>
        </div>
        <div class="mb-3">
            <label for="shortname" class="form-label">Nom court</label>
            <input type="text" class="form-control" id="shortname" name="shortname" value="{{ $course->shortname }}" required>
        </div>
        <div class="mb-3">
            <label for="summary" class="form-label">Résumé</label>
            <textarea class="form-control" id="summary" name="summary">{{ $course->summary }}</textarea>
        </div>
        <div class="mb-3">
            <label for="numsections" class="form-label">Nombre de sections</label>
            <input type="number" class="form-control" id="numsections" name="numsections" value="{{ $course->numsections }}" required>
        </div>
        <div class="mb-3">
            <label for="startdate" class="form-label">Date de début</label>
            <input type="date" class="form-control" id="startdate" name="startdate" value="{{ $course->startdate->format('Y-m-d') }}" required>
        </div>
        <div class="mb-3">
            <label for="enddate" class="form-label">Date de fin</label>
            <input type="date" class="form-control" id="enddate" name="enddate" value="{{ $course->enddate ? $course->enddate->format('Y-m-d') : '' }}">
        </div>
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Enseignant</label>
            <select class="form-select" id="teacher_id" name="teacher_id" required>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ $course->teacher_id == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->username }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
