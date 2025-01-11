@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Course</h1>
    <form action="{{ route('courses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="fullname" class="form-label">Fullname</label>
            <input type="text" name="fullname" class="form-control" id="fullname" required>
        </div>
        <div class="mb-3">
            <label for="shortname" class="form-label">Shortname</label>
            <input type="text" name="shortname" class="form-control" id="shortname" required>
        </div>
        <div class="mb-3">
            <label for="summary" class="form-label">Summary</label>
            <textarea name="summary" class="form-control" id="summary"></textarea>
        </div>
        <div class="mb-3">
            <label for="numsections" class="form-label">Number of Sections</label>
            <input type="number" name="numsections" class="form-control" id="numsections" required>
        </div>
        <div class="mb-3">
            <label for="startdate" class="form-label">Start Date</label>
            <input type="date" name="startdate" class="form-control" id="startdate" required>
        </div>
        <div class="mb-3">
            <label for="enddate" class="form-label">End Date</label>
            <input type="date" name="enddate" class="form-control" id="enddate">
        </div>
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Teacher</label>
            <input type="number" name="teacher_id" class="form-control" id="teacher_id" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
