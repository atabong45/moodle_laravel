@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Create Submission</h1>

    <form action="{{ route('submissions.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="assignment_id" class="block text-sm font-medium">Assignment</label>
            <select name="assignment_id" id="assignment_id" class="form-select mt-1 block w-full">
                @foreach ($assignments as $assignment)
                    <option value="{{ $assignment->id }}">{{ $assignment->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="student_id" class="block text-sm font-medium">Student</label>
            <select name="student_id" id="student_id" class="form-select mt-1 block w-full">
                @foreach ($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="status" class="block text-sm font-medium">Status</label>
            <select name="status" id="status" class="form-select mt-1 block w-full">
                <option value="pending">Pending</option>
                <option value="corrected">Corrected</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="file_path" class="block text-sm font-medium">File Path</label>
            <input type="text" name="file_path" id="file_path" class="form-input mt-1 block w-full">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
