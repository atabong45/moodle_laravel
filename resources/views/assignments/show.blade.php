@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $assignment->name }}</h1>
    <p><strong>Due Date:</strong> {{ $assignment->duedate->format('Y-m-d H:i') }}</p>
    <p><strong>Attempt Number:</strong> {{ $assignment->attemptnumber }}</p>
    <p><strong>Module:</strong> {{ $assignment->module->name }}</p>
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
