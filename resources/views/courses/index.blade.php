@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-1 mx-auto bg-white px-6 py-2 flex flex-col justify-center rounded-2xl">
      <!-- Bouton Retour -->
      <div class="p-2">
                    <a href="{{ url()->previous() }}" 
                    class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Retour
                    </a>
                </div>
    <h1 class='text-3xl font-bold'>Mes cours</h1>
    <hr class="w-full h-[2px] mt-2 mb-3 bg-black" />
@if(auth()->user()->hasRole('ROLE_TEACHER')|| auth()->user()->hasRole('ROLE_ADMIN')){

    <div class='flex w-full gap-4 mb-4'>
        <a href="{{ route('courses.create') }}">
            <x-button>
                Gerer les cours
            </x-button>
        </a>
            <x-button>
                Gerer categories
            </x-button>
        <a class="ml-auto" href="{{ route('courses.create') }}">
            <x-button full='true' href="{{ route('courses.create') }}">
                Créer un cours
            </x-button>
        </a>
    </div>

}
@endif

    <!-- Section de recherche -->
    <div class="mb-4">
        <form action="{{ route('courses.index') }}" method="GET" class="flex items-center gap-2">
            <input type="text" name="search" class="py-2 px-4 border rounded-md w-1/3" placeholder="Rechercher par nom de cours..." value="{{ request()->get('search') }}">
            <x-button type="submit" full="true">
                Rechercher
            </x-button>
        </form>
    </div>


    <h2 class='text-2xl font-bold'>Aperçu</h2>
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
