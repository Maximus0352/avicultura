<?php

namespace App\Policies;

use App\Models\Production;
use App\Models\User;

class ProductionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->estResponsable();
    }

    public function create(User $user): bool
    {
        return $user->estResponsable();
    }

    public function update(User $user, Production $production): bool
    {
        return $user->estResponsable()
            && (int) $user->exploitation_id === (int) $production->exploitation_id;
    }
}