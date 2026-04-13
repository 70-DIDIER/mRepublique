<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'make:admin';
    protected $description = 'Créer un utilisateur administrateur Filament';

    public function handle(): void
    {
        $this->info("Création d'un administrateur Filament");
        $this->newLine();

        $name = $this->ask('Nom');

        $email = $this->askValid('Email', [
            'required', 'email', 'unique:users,email',
        ]);

        $password = $this->secret('Mot de passe (min. 8 caractères)');
        while (strlen($password) < 8) {
            $this->error('Le mot de passe doit contenir au moins 8 caractères.');
            $password = $this->secret('Mot de passe (min. 8 caractères)');
        }

        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
        ]);

        $this->newLine();
        $this->info("✓ Administrateur créé avec succès !");
        $this->table(
            ['Champ', 'Valeur'],
            [
                ['Nom',   $user->name],
                ['Email', $user->email],
                ['Rôle',  $user->role],
            ]
        );
        $this->newLine();
        $this->line("Accès panel : <href=" . url('/admin') . ">" . url('/admin') . "</>");
    }

    private function askValid(string $question, array $rules): string
    {
        $value = $this->ask($question);

        $validator = Validator::make([$question => $value], [$question => $rules]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return $this->askValid($question, $rules);
        }

        return $value;
    }
}
