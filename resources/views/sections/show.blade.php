@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Section Details</h1>
    <p><strong>ID:</strong> {{ $section->id }}</p>
    <p><strong>Name:</strong> {{ $section->name }}</p>
    <p><strong>Course:</strong> {{ $section->course->fullname }}</p>

    <h3>Modules</h3>
    <ul>
        @foreach ($section->modules as $module)
            <li>{{ $module->name }}</li>
        @endforeach
    </ul>

    <a href="{{ route('sections.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
