<?php

namespace Tests\Feature;

use App\Models\Exploitation;
use App\Models\Poulailler;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PoulaillerTest extends TestCase
{
    use DatabaseTransactions;

    private Exploitation $ferme;
    private User $responsable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ferme = Exploitation::factory()->create(['capacite_totale' => 1000]);
        $this->responsable = User::factory()->responsable()->create(['exploitation_id' => $this->ferme->id]);
    }

    private function donnees(array $surcharge = []): array
    {
        return array_merge([
            'code' => 'P1',
            'nom' => 'Poulailler Nord',
            'capacite' => 300,
            'localisation' => 'Nord',
            'etat' => 'actif',
        ], $surcharge);
    }

    public function test_un_responsable_cree_un_poulailler_dans_sa_ferme(): void
    {
        $autre = Exploitation::factory()->create();

        $this->actingAs($this->responsable)
            ->post('/poulaillers', $this->donnees(['exploitation_id' => $autre->id]))
            ->assertRedirect('/poulaillers');

        $this->assertDatabaseHas('poulaillers', ['code' => 'P1', 'exploitation_id' => $this->ferme->id]);
        $this->assertDatabaseMissing('poulaillers', ['exploitation_id' => $autre->id]);
    }

    public function test_le_meme_code_est_autorise_dans_une_autre_ferme(): void
    {
        $autre = Exploitation::factory()->create();
        Poulailler::factory()->create(['exploitation_id' => $autre->id, 'code' => 'P1']);

        $this->actingAs($this->responsable)
            ->post('/poulaillers', $this->donnees())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('poulaillers', 2);
    }

    public function test_un_code_deja_utilise_dans_la_ferme_est_refuse(): void
    {
        Poulailler::factory()->create(['exploitation_id' => $this->ferme->id, 'code' => 'P1', 'capacite' => 100]);

        $this->actingAs($this->responsable)
            ->post('/poulaillers', $this->donnees())
            ->assertSessionHasErrors('code');

        $this->assertDatabaseCount('poulaillers', 1);
    }

    public function test_la_capacite_totale_de_la_ferme_ne_peut_pas_etre_depassee(): void
    {
        Poulailler::factory()->create(['exploitation_id' => $this->ferme->id, 'code' => 'P0', 'capacite' => 800]);

        $this->actingAs($this->responsable);

        $this->post('/poulaillers', $this->donnees(['capacite' => 300]))->assertSessionHasErrors('capacite');
        $this->assertDatabaseMissing('poulaillers', ['code' => 'P1']);

        $this->post('/poulaillers', $this->donnees(['capacite' => 200]))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('poulaillers', ['code' => 'P1', 'capacite' => 200]);
    }

    public function test_la_modification_ne_compte_pas_le_poulailler_deux_fois(): void
    {
        $poulailler = Poulailler::factory()->create(['exploitation_id' => $this->ferme->id, 'capacite' => 800]);

        $this->actingAs($this->responsable);

        $this->put("/poulaillers/{$poulailler->id}", $this->donnees(['code' => $poulailler->code, 'capacite' => 1000]))
            ->assertSessionHasNoErrors();
        $this->assertSame(1000, $poulailler->fresh()->capacite);

        $this->put("/poulaillers/{$poulailler->id}", $this->donnees(['code' => $poulailler->code, 'capacite' => 1001]))
            ->assertSessionHasErrors('capacite');
        $this->assertSame(1000, $poulailler->fresh()->capacite);
    }

    public function test_un_poulailler_d_une_autre_ferme_est_introuvable(): void
    {
        $autre = Exploitation::factory()->create();
        $etranger = Poulailler::factory()->create(['exploitation_id' => $autre->id]);

        $this->actingAs($this->responsable);

        $this->get("/poulaillers/{$etranger->id}/modifier")->assertNotFound();
        $this->put("/poulaillers/{$etranger->id}", $this->donnees(['capacite' => 100]))->assertNotFound();
        $this->get('/poulaillers')->assertOk()->assertDontSee($etranger->code . '</td>', false);
    }

    public function test_un_agent_et_un_admin_n_ont_pas_acces(): void
    {
        $this->actingAs(User::factory()->agent()->create(['exploitation_id' => $this->ferme->id]))
            ->get('/poulaillers')
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->post('/poulaillers', $this->donnees())
            ->assertForbidden();

        $this->assertDatabaseCount('poulaillers', 0);
    }

    public function test_une_ferme_inactive_ne_peut_pas_creer_de_poulailler(): void
    {
        $this->ferme->update(['statut' => 'inactive']);

        $this->actingAs($this->responsable)
            ->post('/poulaillers', $this->donnees())
            ->assertSessionHas('erreur');

        $this->assertDatabaseCount('poulaillers', 0);
    }
}