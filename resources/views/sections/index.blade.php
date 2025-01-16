@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sections</h1>
    <a href="{{ route('sections.create') }}" class="btn btn-primary mb-3">Add Section</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sections as $section)
                <tr>
                    <td>{{ $section->id }}</td>
                    <td>{{ $section->name }}</td>
                    <td>{{ $section->course->fullname }}</td>
                    <td>
                        <a href="{{ route('sections.show', $section) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('sections.edit', $section) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('sections.destroy', $section) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
