<?php

namespace App\Policies;

use App\Models\BandeElevage;
use App\Models\User;

class BandeElevagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->estResponsable() || $user->estAgent();
    }

    public function view(User $user, BandeElevage $bande): bool
    {
        return ($user->estResponsable() || $user->estAgent())
            && (int) $user->exploitation_id === (int) $bande->exploitation_id;
    }

    public function create(User $user): bool
    {
        return $user->estResponsable();
    }

    public function update(User $user, BandeElevage $bande): bool
    {
        return $user->estResponsable()
            && (int) $user->exploitation_id === (int) $bande->exploitation_id
            && in_array($bande->statut, ['planifiee', 'en_cours'], true);
    }

    public function changerStatut(User $user, BandeElevage $bande): bool
    {
        return $user->estResponsable()
            && (int) $user->exploitation_id === (int) $bande->exploitation_id;
    }
}