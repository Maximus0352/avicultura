<?php

namespace App\Policies;

use App\Models\Poulailler;
use App\Models\User;

class PoulaillerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->estResponsable();
    }

    public function create(User $user): bool
    {
        return $user->estResponsable();
    }

    public function update(User $user, Poulailler $poulailler): bool
    {
        return $user->estResponsable()
            && (int) $user->exploitation_id === (int) $poulailler->exploitation_id;
    }
}