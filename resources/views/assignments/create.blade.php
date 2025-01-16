@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Assignment</h1>
    <form action="{{ route('assignments.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="duedate" class="form-label">Due Date</label>
            <input type="datetime-local" name="duedate" id="duedate" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="attemptnumber" class="form-label">Attempt Number</label>
            <input type="number" name="attemptnumber" id="attemptnumber" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="module_id" class="form-label">Module</label>
            <select name="module_id" id="module_id" class="form-control" required>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
