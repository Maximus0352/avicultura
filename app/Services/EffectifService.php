<?php

namespace App\Services;

use App\Models\BandeElevage;
use Carbon\CarbonInterface;

class EffectifService
{
    public function vivant(BandeElevage $bande, ?CarbonInterface $date = null): int
    {
        $limite = ($date ?? today())->toDateString();

        $morts = (int) $bande->mortalites()->where('date_mortalite', '<=', $limite)->sum('quantite');
        $vendus = (int) $bande->ventes()->where('date_vente', '<=', $limite)->sum('nombre_sujets');

        return $bande->effectif_initial - $morts - $vendus;
    }

    public function depuisCumuls(BandeElevage $bande): int
    {
        return $bande->effectif_initial
            - (int) ($bande->mortalites_sum_quantite ?? 0)
            - (int) ($bande->ventes_sum_nombre_sujets ?? 0);
    }

    public function presentDans(int $poulaillerId): int
    {
        return BandeElevage::where('poulailler_id', $poulaillerId)
            ->where('statut', 'en_cours')
            ->withSum('mortalites', 'quantite')
            ->withSum('ventes', 'nombre_sujets')
            ->get()
            ->sum(fn (BandeElevage $bande) => $this->depuisCumuls($bande));
    }
}