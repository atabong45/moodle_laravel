@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Submission Details</h1>
    <p><strong>ID:</strong> {{ $submission->id }}</p>
    <p><strong>Status:</strong> {{ $submission->status }}</p>
    <p><strong>File Path:</strong> {{ $submission->file_path }}</p>
    <p><strong>Assignment:</strong> {{ $submission->assignment->name }}</p>
    <p><strong>Student:</strong> {{ $submission->student->username }}</p>
    <a href="{{ route('submissions.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
