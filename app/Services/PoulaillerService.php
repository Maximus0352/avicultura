<?php

namespace App\Services;

use App\Models\Exploitation;
use App\Models\Poulailler;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PoulaillerService
{
    public function creer(User $acteur, array $donnees): Poulailler
    {
        return DB::transaction(function () use ($acteur, $donnees) {
            $this->verifierCapacite($acteur->exploitation_id, (int) $donnees['capacite']);

            return Poulailler::create([
                ...$donnees,
                'exploitation_id' => $acteur->exploitation_id,
            ]);
        });
    }

    public function modifier(Poulailler $poulailler, array $donnees): Poulailler
    {
        return DB::transaction(function () use ($poulailler, $donnees) {
            $this->verifierCapacite($poulailler->exploitation_id, (int) $donnees['capacite'], $poulailler->id);

            $poulailler->update($donnees);

            return $poulailler;
        });
    }

    private function verifierCapacite(int $exploitationId, int $capacite, ?int $exclureId = null): void
    {
        $exploitation = Exploitation::whereKey($exploitationId)->lockForUpdate()->firstOrFail();

        $autres = (int) Poulailler::withoutGlobalScopes()
            ->where('exploitation_id', $exploitationId)
            ->when($exclureId, fn ($requete) => $requete->where('id', '<>', $exclureId))
            ->sum('capacite');

        if ($autres + $capacite > $exploitation->capacite_totale) {
            throw ValidationException::withMessages([
                'capacite' => 'La somme des capacités des poulaillers (' . ($autres + $capacite)
                    . ') dépasserait la capacité totale de l\'exploitation (' . $exploitation->capacite_totale . ').',
            ]);
        }
    }
}