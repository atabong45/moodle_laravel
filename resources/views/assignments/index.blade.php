@extends('layouts.app')

@section('title', 'Liste des Assignments')

@section('content')
<div class="container">
    <h1 class="mb-4">Assignments</h1>
    <a href="{{ route('assignments.create') }}" class="btn btn-primary mb-3">Create Assignment</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Due Date</th>
                <th>Attempt Number</th>
                <th>Module</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->name }}</td>
                    <td>{{ $assignment->duedate->format('Y-m-d H:i') }}</td>
                    <td>{{ $assignment->attemptnumber }}</td>
                    <td>{{ $assignment->module->name }}</td>
                    <td>
                        <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No assignments available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
