<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'make:admin';
    protected $description = 'Créer un administrateur avec rôle dans le champ user.role';

    public function handle()
    {
        $name = $this->ask('Nom complet');
        $email = $this->ask('Email');
        
        // Vérifier s’il existe déjà
        if (User::where('email', $email)->exists()) {
            $this->error("Un utilisateur avec cet email existe déjà.");
            return;
        }

        $password = $this->secret('Mot de passe');
        $confirmPassword = $this->secret('Confirmer le mot de passe');

        if ($password !== $confirmPassword) {
            $this->error("Les mots de passe ne correspondent pas.");
            return;
        }

        // Création de l'utilisateur
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("Administrateur créé avec succès : {$user->email}");
    }
}
