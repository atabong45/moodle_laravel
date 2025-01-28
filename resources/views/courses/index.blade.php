@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto flex flex-col justify-center">
    <h1 class='text-3xl font-bold'>My courses</h1>
    <hr class="w-full h-[2px] mt-2 mb-4 bg-black" />
    <div class='flex w-full gap-4 mb-6'>
        <x-button>
            Manage courses
        </x-button>
        <x-button>
            Manage categories
        </x-button>
        <a class="ml-auto" href="{{ route('courses.create') }}">
            <x-button full='true' href="{{ route('courses.create') }}">
                Create a course
            </x-button>
        </a>
    </div>

    <!-- Section de recherche -->
    <div class="mb-6">
        <form action="{{ route('courses.index') }}" method="GET" class="flex items-center gap-2">
            <input type="text" name="search" class="py-2 px-4 border rounded-md w-1/3" placeholder="Search by course name..." value="{{ request()->get('search') }}">
            <x-button type="submit" full="true">
                Search
            </x-button>
        </form>
    </div>


    <h2 class='text-2xl font-bold'>Overview</h2>
    <hr class="w-3/5 h-[3px] mb-4 bg-black" />
    <div class='flex flex-col gap-4 pl-2'>
        <div class="mb-4">
            <h3 class='text-xl text-primary mb-2'>{{ strtoupper('Vos cours') }}</h3>
            <div class="grid grid-cols-4 gap-4">
                @foreach($courses as $course)
                    <x-course :course="$course"/>
                @endforeach
            </div>
        </div>
        <div class="mb-4">
            <h3 class='text-xl text-primary mb-2'>{{ strtoupper('Cours disponibles') }}</h3>
            <div class="grid grid-cols-4 gap-4">
                @foreach($courses as $course)
                    <x-course :course="$course"/>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
