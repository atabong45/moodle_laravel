@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Submissions</h1>
    <a href="{{ route('submissions.create') }}" class="btn btn-primary mb-3">Create New Submission</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>File Path</th>
                <th>Assignment</th>
                <th>Student</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $submission)
                <tr>
                    <td>{{ $submission->id }}</td>
                    <td>{{ $submission->status }}</td>
                    <td>{{ $submission->file_path }}</td>
                    <td>{{ $submission->assignment->name }}</td>
                    <td>{{ $submission->student->username }}</td>
                    <td>
                        <a href="{{ route('submissions.show', $submission) }}" class="btn btn-info">View</a>
                        <a href="{{ route('submissions.edit', $submission) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('submissions.destroy', $submission) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
