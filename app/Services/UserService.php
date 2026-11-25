<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function creer(User $acteur, array $donnees): User
    {
        $utilisateur = new User([
            'nom' => $donnees['nom'],
            'email' => $donnees['email'],
            'password' => $donnees['password'],
        ]);

        $utilisateur->forceFill([
            'role' => $acteur->estAdmin() ? 'responsable' : 'agent',
            'exploitation_id' => $acteur->estAdmin() ? $donnees['exploitation_id'] : $acteur->exploitation_id,
            'actif' => true,
        ])->save();

        return $utilisateur;
    }

    public function modifier(User $utilisateur, array $donnees): User
    {
        $utilisateur->nom = $donnees['nom'];
        $utilisateur->email = $donnees['email'];

        if (! empty($donnees['password'])) {
            $utilisateur->password = $donnees['password'];
        }

        $utilisateur->save();

        return $utilisateur;
    }

    public function basculerActif(User $utilisateur): User
    {
        $utilisateur->update(['actif' => ! $utilisateur->actif]);

        return $utilisateur;
    }
}