<?php

namespace Tests\Feature;

use App\Models\BandeElevage;
use App\Models\Exploitation;
use App\Models\Mortalite;
use App\Models\Vente;
use App\Services\EffectifService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EffectifServiceTest extends TestCase
{
    use DatabaseTransactions;

    private Exploitation $ferme;
    private BandeElevage $bande;
    private EffectifService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new EffectifService();
        $this->ferme = Exploitation::factory()->create();
        $this->bande = BandeElevage::factory()->create([
            'exploitation_id' => $this->ferme->id,
            'effectif_initial' => 500,
            'date_debut' => now()->subDays(10)->toDateString(),
        ]);

        $this->mortalite(20, -5);
        $this->mortalite(10, -1);
        $this->vente(100, -2);
    }

    private function mortalite(int $quantite, int $jours): void
    {
        Mortalite::factory()->create([
            'exploitation_id' => $this->ferme->id,
            'bande_id' => $this->bande->id,
            'quantite' => $quantite,
            'date_mortalite' => now()->addDays($jours)->toDateString(),
        ]);
    }

    private function vente(int $nombre, int $jours): void
    {
        Vente::factory()->create([
            'exploitation_id' => $this->ferme->id,
            'bande_id' => $this->bande->id,
            'nombre_sujets' => $nombre,
            'date_vente' => now()->addDays($jours)->toDateString(),
        ]);
    }

    public function test_l_effectif_vivant_depend_de_la_date(): void
    {
        $this->assertSame(500, $this->service->vivant($this->bande, now()->subDays(6)));
        $this->assertSame(480, $this->service->vivant($this->bande, now()->subDays(5)));
        $this->assertSame(380, $this->service->vivant($this->bande, now()->subDays(2)));
        $this->assertSame(370, $this->service->vivant($this->bande));
    }

    public function test_les_cumuls_donnent_le_meme_effectif_que_le_calcul_direct(): void
    {
        $bande = BandeElevage::withSum('mortalites', 'quantite')
            ->withSum('ventes', 'nombre_sujets')
            ->findOrFail($this->bande->id);

        $this->assertSame(370, $this->service->depuisCumuls($bande));
        $this->assertSame($this->service->vivant($this->bande), $this->service->depuisCumuls($bande));
    }

    public function test_l_effectif_present_ne_compte_que_les_bandes_en_cours(): void
    {
        $this->bande->update(['statut' => 'en_cours']);
        BandeElevage::factory()->create([
            'exploitation_id' => $this->ferme->id,
            'poulailler_id' => $this->bande->poulailler_id,
            'statut' => 'terminee',
            'effectif_initial' => 300,
        ]);

        $this->assertSame(370, $this->service->presentDans($this->bande->poulailler_id));
    }
}