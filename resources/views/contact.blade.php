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

    <h1 class="text-3xl font-bold">Contactez-nous</h1>
    <hr class="w-full h-[2px] mt-2 mb-4 bg-black" />

    <p class="text-lg mb-4">
        Une question, une suggestion ou besoin d'aide ? N'hésitez pas à nous contacter, notre équipe est à votre disposition !
    </p>

    <h2 class="text-2xl font-bold mt-6">Nos Coordonnées</h2>
    <hr class="w-3/5 h-[3px] mb-4 bg-black" />
    <p class="text-lg">
        <strong>Email :</strong> <a href="mailto:ouendeufranck@gmail.com" class="text-blue-500 hover:underline">ouendeufranck@gmail.com</a>
    </p>
    <p class="text-lg">
        <strong>Téléphone :</strong> +237 65 05 44 13
    </p>
    <p class="text-lg">
        <strong>Adresse :</strong> ENSPY, 8390 Yaoundé-Cameroun
    </p>

    <h2 class="text-2xl font-bold mt-12">Envoyer un Message</h2>
    <hr class="w-3/5 h-[3px] mb-1 bg-black" />

    <form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="message" class="block font-medium">Message</label>
            <textarea id="message" name="message" rows="5" required class="w-full p-2 border rounded-md"></textarea>
        </div>
        <x-button type="submit" full="true">
            Envoyer
        </x-button>
    </form>
</div>
@endsection
