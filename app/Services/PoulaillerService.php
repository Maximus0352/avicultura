<?php

namespace App\Services;

use App\Models\Exploitation;
use App\Models\Poulailler;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PoulaillerService
{
    public function __construct(private EffectifService $effectifs)
    {
    }

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
            $verrouille = Poulailler::whereKey($poulailler->id)->lockForUpdate()->firstOrFail();

            $this->verifierCapacite($verrouille->exploitation_id, (int) $donnees['capacite'], $verrouille->id);

            $present = $this->effectifs->presentDans($verrouille->id);

            if ((int) $donnees['capacite'] < $present) {
                throw ValidationException::withMessages([
                    'capacite' => 'La capacité ne peut pas être inférieure à l\'effectif actuellement présent (' . $present . ').',
                ]);
            }

            $verrouille->update($donnees);

            return $verrouille;
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