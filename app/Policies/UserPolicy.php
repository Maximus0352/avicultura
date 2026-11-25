<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class UserPolicy
{
    public function viewAny(User $acteur): bool
    {
        return $acteur->estAdmin() || $acteur->estResponsable();
    }

    public function create(User $acteur): bool
    {
        return $acteur->estAdmin() || $acteur->estResponsable();
    }

    public function update(User $acteur, User $cible): Response|bool
    {
        if ($acteur->estAdmin()) {
            return $cible->role !== 'admin';
        }

        if ($acteur->estResponsable()) {
            if ((int) $cible->exploitation_id !== (int) $acteur->exploitation_id) {
                Log::warning('Acces inter-exploitation refuse', [
                    'acteur_id' => $acteur->id,
                    'cible_id' => $cible->id,
                ]);

                return Response::denyAsNotFound();
            }

            return $cible->role === 'agent';
        }

        return false;
    }

    public function changerStatut(User $acteur, User $cible): Response|bool
    {
        if ($acteur->is($cible)) {
            return false;
        }

        return $this->update($acteur, $cible);
    }
}