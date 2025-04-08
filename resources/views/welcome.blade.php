@extends('layouts.app')

@section('title', 'Page d\'accueil')

@section('content')
    <div class="container mx-auto text-center py-12">
        <h1 class="text-4xl font-bold text-black">Bienvenue sur notre site d'apprentissage</h1>
        <p class="mt-4 text-lg text-gray-600">Explorez nos services et découvrez des ressources pour améliorer vos compétences.</p>
        
        <!-- Section Services -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-black">Nos Cours</h2>
                <p class="mt-2 text-gray-600">Accédez à une variété de cours pour améliorer vos compétences dans divers domaines.</p>
                <a href="/courses" class="mt-4 inline-block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Voir les cours</a>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-black">À propos de nous</h2>
                <p class="mt-2 text-gray-600">Apprenez-en plus sur notre mission et notre équipe dédiée à vous aider à réussir.</p>
                <a href="/about" class="mt-4 inline-block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">En savoir plus</a>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-black">Contactez-nous</h2>
                <p class="mt-2 text-gray-600">Vous avez des questions ? N'hésitez pas à nous contacter pour toute information.</p>
                <a href="/contact" class="mt-4 inline-block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Nous contacter</a>
            </div>
        </div>

        <!-- Section Call to Action -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-black">Rejoignez-nous dès aujourd'hui !</h2>
            <p class="mt-4 text-lg text-gray-600">Inscrivez-vous pour accéder à nos ressources et commencer votre parcours d'apprentissage.</p>
        </div>
    </div>
@endsection
