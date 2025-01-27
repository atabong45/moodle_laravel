@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Assignment</h1>
    <x-form :action="route('assignments.store')" method="POST" buttonText="Create" />
</div>
@endsection
