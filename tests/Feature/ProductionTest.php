<?php

namespace Tests\Feature;

use App\Models\Exploitation;
use App\Models\Production;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductionTest extends TestCase
{
    use DatabaseTransactions;

    private Exploitation $ferme;
    private User $responsable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ferme = Exploitation::factory()->create();
        $this->responsable = User::factory()->responsable()->create(['exploitation_id' => $this->ferme->id]);
    }

    private function donnees(array $surcharge = []): array
    {
        return array_merge([
            'nom' => 'Poulet de chair',
            'description' => 'Cycle de 45 jours',
            'unite_production' => 'sujet',
        ], $surcharge);
    }

    public function test_un_responsable_cree_une_production_dans_sa_ferme(): void
    {
        $autre = Exploitation::factory()->create();

        $this->actingAs($this->responsable)
            ->post('/productions', $this->donnees(['exploitation_id' => $autre->id]))
            ->assertRedirect('/productions');

        $this->assertDatabaseHas('productions', ['nom' => 'Poulet de chair', 'exploitation_id' => $this->ferme->id]);
    }

    public function test_un_nom_deja_utilise_dans_la_ferme_est_refuse(): void
    {
        Production::factory()->create(['exploitation_id' => $this->ferme->id, 'nom' => 'Poulet de chair']);

        $this->actingAs($this->responsable)
            ->post('/productions', $this->donnees())
            ->assertSessionHasErrors('nom');

        $this->assertDatabaseCount('productions', 1);
    }

    public function test_le_meme_nom_est_autorise_dans_une_autre_ferme(): void
    {
        $autre = Exploitation::factory()->create();
        Production::factory()->create(['exploitation_id' => $autre->id, 'nom' => 'Poulet de chair']);

        $this->actingAs($this->responsable)
            ->post('/productions', $this->donnees())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('productions', 2);
    }

    public function test_une_unite_hors_liste_est_refusee(): void
    {
        $this->actingAs($this->responsable)
            ->post('/productions', $this->donnees(['unite_production' => 'tonne']))
            ->assertSessionHasErrors('unite_production');

        $this->assertDatabaseCount('productions', 0);
    }

    public function test_une_production_d_une_autre_ferme_est_introuvable(): void
    {
        $autre = Exploitation::factory()->create();
        $etrangere = Production::factory()->create(['exploitation_id' => $autre->id]);

        $this->actingAs($this->responsable);

        $this->get("/productions/{$etrangere->id}/modifier")->assertNotFound();
        $this->put("/productions/{$etrangere->id}", $this->donnees(['nom' => 'Piratage']))->assertNotFound();
        $this->assertNotSame('Piratage', $etrangere->fresh()->nom);
    }
}