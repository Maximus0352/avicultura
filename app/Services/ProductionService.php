<?php

namespace App\Services;

use App\Models\Production;
use App\Models\User;

class ProductionService
{
    public function creer(User $acteur, array $donnees): Production
    {
        return Production::create([
            ...$donnees,
            'exploitation_id' => $acteur->exploitation_id,
        ]);
    }

    public function modifier(Production $production, array $donnees): Production
    {
        $production->update($donnees);

        return $production;
    }
}