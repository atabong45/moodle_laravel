@extends('layouts.app')

@section('content')
<div class="container w-4/5 mt-10 mx-auto bg-white p-6 flex flex-col justify-center rounded-2xl">
    <!-- Bouton Retour -->
    <div class="p-1">
        <a href="{{ url()->previous() }}"
        class="text-blue-500 hover:text-blue-700 font-medium mb-4 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>

    <h1 class="text-3xl font-bold">À propos</h1>
    <hr class="w-full h-[2px] mt-2 mb-4 bg-black" />

    <p class="text-lg mb-4">
        Bienvenue sur notre plateforme ! Nous nous engageons à offrir une expérience d'apprentissage enrichissante
        et accessible à tous. Que vous soyez étudiant, enseignant ou simple curieux, vous trouverez ici une multitude
        de ressources pour élargir vos connaissances.
    </p>

    <h2 class="text-2xl font-bold mt-6">Notre Mission</h2>
    <hr class="w-3/5 h-[3px] mb-4 bg-black" />
    <p class="text-lg mb-4">
        Notre mission est de faciliter l'accès au savoir en proposant une plateforme intuitive et complète
        pour l'éducation en ligne. Nous croyons que l'apprentissage doit être interactif, engageant et disponible pour tous.
    </p>

    <h2 class="text-2xl font-bold mt-6">Notre Équipe</h2>
    <hr class="w-3/5 h-[3px] mb-4 bg-black" />
    <div class="grid grid-cols-3 gap-6">
        <div class="text-center">
            <img src="{{ asset('images/team/jean.jpg') }}" alt="Jean Dupont" class="mx-auto rounded-full w-40 h-40 object-cover">
            <h3 class="text-xl font-bold mt-3">Jean Dupont</h3>
            <p class="text-gray-600">Fondateur & CEO</p>
        </div>
        <div class="text-center">
            <img src="{{ asset('images/team/marie.jpg') }}" alt="Marie Curie" class="mx-auto rounded-full w-40 h-40 object-cover">
            <h3 class="text-xl font-bold mt-3">Marie Curie</h3>
            <p class="text-gray-600">Responsable pédagogique</p>
        </div>
        <div class="text-center">
            <img src="{{ asset('images/team/pierre.jpg') }}" alt="Pierre Martin" class="mx-auto rounded-full w-40 h-40 object-cover">
            <h3 class="text-xl font-bold mt-3">Pierre Martin</h3>
            <p class="text-gray-600">Développeur principal</p>
        </div>
    </div>

    <h2 class="text-2xl font-bold mt-6">Contact</h2>
    <hr class="w-3/5 h-[3px] mb-4 bg-black" />
    <p class="text-lg">
        Si vous avez des questions, n'hésitez pas à nous contacter à
        <a href="/contact" class="text-blue-500 hover:underline">ouendeufranck@gmail</a>.
    </p>
</div>
@endsection
