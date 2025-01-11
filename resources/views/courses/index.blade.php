@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Courses</h1>
    <a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">Create Course</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Fullname</th>
                <th>Shortname</th>
                <th>Teacher</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
                <tr>
                    <td>{{ $course->id }}</td>
                    <td>{{ $course->fullname }}</td>
                    <td>{{ $course->shortname }}</td>
                    <td>{{ $course->teacher->username ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('courses.destroy', $course) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
