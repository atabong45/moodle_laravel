<!-- resources/views/admin/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Créer un nouvel utilisateur</h2>

    <!-- Afficher un message de succès si l'utilisateur a été créé -->
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- Formulaire de création d'utilisateur -->
    <form action="{{ route('admin.store') }}" method="POST">
        @csrf

        <!-- Nom de l'utilisateur -->
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email de l'utilisateur -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mot de passe de l'utilisateur -->
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirmation du mot de passe -->
        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <!-- Choix du rôle -->
        <div class="form-group">
            <label for="role">Rôle</label>
            <select name="role" id="role" class="form-control" required>
                <option value="ROLE_STUDENT">Étudiant</option>
                <option value="ROLE_TEACHER">Enseignant</option>
            </select>
            @error('role')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary mt-3">Créer l'utilisateur</button>
    </form>
</div>
@endsection
