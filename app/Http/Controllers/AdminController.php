<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
   
    // Afficher le formulaire pour créer un utilisateur
    public function create()
    {
        // Récupérer les rôles disponibles
        $roles = Role::all();
        return view('admin.create', compact('roles'));
    }

    // Sauvegarder un nouvel utilisateur
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:ROLE_STUDENT,ROLE_TEACHER',
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assigner le rôle à l'utilisateur
        $user->assignRole($request->role);

        // Rediriger avec un message de succès
        return redirect()->route('admin.create')->with('status', 'Utilisateur créé avec succès!');
    }
}
