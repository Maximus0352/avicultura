<?php

namespace App\Services;

use App\Models\BandeElevage;
use App\Models\Poulailler;
use App\Models\Production;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BandeElevageService
{
    public function creer(User $acteur, array $donnees): BandeElevage
    {
        return DB::transaction(function () use ($acteur, $donnees) {
            $poulailler = $this->verrouillerPoulailler((int) $donnees['poulailler_id']);

            $this->verifierProduction((int) $donnees['production_id']);
            $this->verifierAccueil($poulailler, (int) $donnees['effectif_initial']);
            $this->verifierChevauchement(
                $poulailler->id,
                Carbon::parse($donnees['date_debut'])->startOfDay(),
                Carbon::parse($donnees['date_fin_prevue'])->startOfDay(),
                null
            );

            return BandeElevage::create([
                ...$donnees,
                'statut' => 'planifiee',
                'exploitation_id' => $acteur->exploitation_id,
            ]);
        });
    }

    public function modifier(BandeElevage $bande, array $donnees): BandeElevage
    {
        return DB::transaction(function () use ($bande, $donnees) {
            $bande = $this->verrouillerBande($bande);

            if ($bande->statut === 'en_cours') {
                $poulailler = $this->verrouillerPoulailler($bande->poulailler_id);
                $fin = Carbon::parse($donnees['date_fin_prevue'])->startOfDay();

                if ($fin->lte($bande->date_debut)) {
                    throw ValidationException::withMessages([
                        'date_fin_prevue' => 'La date de fin prévue doit être postérieure à la date de début.',
                    ]);
                }

                $this->verifierChevauchement($poulailler->id, $bande->date_debut, $this->finOccupee('en_cours', $fin), $bande->id);
                $bande->update(['date_fin_prevue' => $fin->toDateString()]);

                return $bande;
            }

            $this->exigerStatut($bande, ['planifiee'], 'Une bande terminée ou annulée n\'est plus modifiable.');

            $poulailler = $this->verrouillerPoulailler((int) $donnees['poulailler_id']);

            $this->verifierProduction((int) $donnees['production_id']);
            $this->verifierAccueil($poulailler, (int) $donnees['effectif_initial']);
            $this->verifierChevauchement(
                $poulailler->id,
                Carbon::parse($donnees['date_debut'])->startOfDay(),
                Carbon::parse($donnees['date_fin_prevue'])->startOfDay(),
                $bande->id
            );

            $bande->update($donnees);

            return $bande;
        });
    }

    public function demarrer(BandeElevage $bande): BandeElevage
    {
        return DB::transaction(function () use ($bande) {
            $bande = $this->verrouillerBande($bande);

            $this->exigerStatut($bande, ['planifiee'], 'Seule une bande planifiée peut démarrer.');

            if ($bande->date_debut->gt(Carbon::today())) {
                throw ValidationException::withMessages([
                    'date_debut' => 'Une bande ne peut pas démarrer avant sa date de début (' . $bande->date_debut->format('d/m/Y') . ').',
                ]);
            }

            $poulailler = $this->verrouillerPoulailler($bande->poulailler_id);

            $this->verifierAccueil($poulailler, $bande->effectif_initial);
            $this->verifierChevauchement(
                $poulailler->id,
                $bande->date_debut,
                $this->finOccupee('en_cours', $bande->date_fin_prevue),
                $bande->id
            );

            $bande->update(['statut' => 'en_cours']);

            return $bande;
        });
    }

    public function terminer(BandeElevage $bande, string $dateFinReelle): BandeElevage
    {
        return DB::transaction(function () use ($bande, $dateFinReelle) {
            $bande = $this->verrouillerBande($bande);

            $this->exigerStatut($bande, ['en_cours'], 'Seule une bande en cours peut être terminée.');

            $date = Carbon::parse($dateFinReelle)->startOfDay();

            if ($date->lt($bande->date_debut)) {
                throw ValidationException::withMessages([
                    'date_fin_reelle' => 'La date de fin réelle ne peut pas précéder la date de début.',
                ]);
            }

            if ($date->gt(Carbon::today())) {
                throw ValidationException::withMessages([
                    'date_fin_reelle' => 'La date de fin réelle ne peut pas être dans le futur.',
                ]);
            }

            $bande->update(['statut' => 'terminee', 'date_fin_reelle' => $date->toDateString()]);

            return $bande;
        });
    }

    public function annuler(BandeElevage $bande): BandeElevage
    {
        return DB::transaction(function () use ($bande) {
            $bande = $this->verrouillerBande($bande);

            $this->exigerStatut($bande, ['planifiee', 'en_cours'], 'Une bande terminée ou annulée ne peut plus être annulée.');

            $bande->update(['statut' => 'annulee']);

            return $bande;
        });
    }

    private function verrouillerBande(BandeElevage $bande): BandeElevage
    {
        return BandeElevage::whereKey($bande->id)->lockForUpdate()->firstOrFail();
    }

    private function verrouillerPoulailler(int $id): Poulailler
    {
        $poulailler = Poulailler::whereKey($id)->lockForUpdate()->first();

        if ($poulailler === null) {
            throw ValidationException::withMessages(['poulailler_id' => 'Poulailler introuvable.']);
        }

        return $poulailler;
    }

    private function verifierProduction(int $id): void
    {
        if (! Production::whereKey($id)->exists()) {
            throw ValidationException::withMessages(['production_id' => 'Production introuvable.']);
        }
    }

    private function verifierAccueil(Poulailler $poulailler, int $effectif): void
    {
        if ($poulailler->etat !== 'actif') {
            throw ValidationException::withMessages([
                'poulailler_id' => 'Ce poulailler est en maintenance ou inactif : il ne peut pas recevoir de bande.',
            ]);
        }

        if ($effectif > $poulailler->capacite) {
            throw ValidationException::withMessages([
                'effectif_initial' => 'L\'effectif initial dépasse la capacité du poulailler (' . $poulailler->capacite . ').',
            ]);
        }
    }

    private function finOccupee(string $statut, Carbon $finPrevue): Carbon
    {
        if ($statut === 'en_cours' && $finPrevue->lt(Carbon::today())) {
            return Carbon::create(9999, 12, 31)->startOfDay();
        }

        return $finPrevue;
    }

    private function verifierChevauchement(int $poulaillerId, Carbon $debut, Carbon $fin, ?int $exclureId): void
    {
        $autres = BandeElevage::where('poulailler_id', $poulaillerId)
            ->whereIn('statut', ['planifiee', 'en_cours'])
            ->when($exclureId, fn ($requete) => $requete->where('id', '<>', $exclureId))
            ->get();

        foreach ($autres as $autre) {
            $finAutre = $this->finOccupee($autre->statut, $autre->date_fin_prevue);

            if ($debut->lte($finAutre) && $fin->gte($autre->date_debut)) {
                $periode = $finAutre->year === 9999
                    ? 'depuis le ' . $autre->date_debut->format('d/m/Y') . ', sans date de fin'
                    : 'du ' . $autre->date_debut->format('d/m/Y') . ' au ' . $finAutre->format('d/m/Y');

                throw ValidationException::withMessages([
                    'date_debut' => "Le poulailler est déjà occupé par la bande {$autre->code} ({$periode}).",
                ]);
            }
        }
    }

    private function exigerStatut(BandeElevage $bande, array $autorises, string $message): void
    {
        if (! in_array($bande->statut, $autorises, true)) {
            throw ValidationException::withMessages(['statut' => $message]);
        }
    }
}