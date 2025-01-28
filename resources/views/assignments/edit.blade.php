@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Assignment</h1>
    <form action="{{ route('assignments.update', $assignment) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $assignment->name }}" required>
        </div>
        <div class="mb-3">
            <label for="duedate" class="form-label">Due Date</label>
            <input type="datetime-local" name="duedate" id="duedate" class="form-control" value="{{ $assignment->duedate->format('Y-m-d\TH:i') }}" required>
        </div>
        <div class="mb-3">
            <label for="attemptnumber" class="form-label">Attempt Number</label>
            <input type="number" name="attemptnumber" id="attemptnumber" class="form-control" value="{{ $assignment->attemptnumber }}" required>
        </div>
        <div class="mb-3">
            <label for="module_id" class="form-label">Module</label>
            <select name="module_id" id="module_id" class="form-control" required>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ $assignment->module_id == $module->id ? 'selected' : '' }}>
                        {{ $module->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
