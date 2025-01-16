@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Submission</h1>
    <form action="{{ route('submissions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <input type="text" name="status" id="status" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="file_path" class="form-label">File Path</label>
            <input type="text" name="file_path" id="file_path" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="assignment_id" class="form-label">Assignment</label>
            <select name="assignment_id" id="assignment_id" class="form-select" required>
                @foreach($assignments as $assignment)
                    <option value="{{ $assignment->id }}">{{ $assignment->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="student_id" class="form-label">Student</label>
            <select name="student_id" id="student_id" class="form-select" required>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->username }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
