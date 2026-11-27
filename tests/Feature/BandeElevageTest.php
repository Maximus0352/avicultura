<?php

namespace Tests\Feature;

use App\Models\BandeElevage;
use App\Models\Exploitation;
use App\Models\Mortalite;
use App\Models\Poulailler;
use App\Models\Production;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BandeElevageTest extends TestCase
{
    use DatabaseTransactions;

    private Exploitation $ferme;
    private User $responsable;
    private User $agent;
    private Poulailler $poulailler;
    private Production $production;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ferme = Exploitation::factory()->create(['capacite_totale' => 5000]);
        $this->responsable = User::factory()->responsable()->create(['exploitation_id' => $this->ferme->id]);
        $this->agent = User::factory()->agent()->create(['exploitation_id' => $this->ferme->id]);
        $this->poulailler = Poulailler::factory()->create(['exploitation_id' => $this->ferme->id, 'capacite' => 1000]);
        $this->production = Production::factory()->create(['exploitation_id' => $this->ferme->id]);
    }

    private function jour(int $decalage): string
    {
        return now()->addDays($decalage)->toDateString();
    }

    private function donnees(array $surcharge = []): array
    {
        return array_merge([
            'poulailler_id' => $this->poulailler->id,
            'production_id' => $this->production->id,
            'code' => 'B1',
            'date_debut' => $this->jour(5),
            'date_fin_prevue' => $this->jour(50),
            'effectif_initial' => 500,
        ], $surcharge);
    }

    private function bande(array $surcharge = []): BandeElevage
    {
        return BandeElevage::factory()->create(array_merge([
            'exploitation_id' => $this->ferme->id,
            'poulailler_id' => $this->poulailler->id,
            'production_id' => $this->production->id,
            'statut' => 'planifiee',
            'date_debut' => $this->jour(5),
            'date_fin_prevue' => $this->jour(50),
            'effectif_initial' => 500,
        ], $surcharge));
    }

    public function test_un_responsable_cree_une_bande_planifiee_dans_sa_ferme(): void
    {
        $autre = Exploitation::factory()->create();

        $this->actingAs($this->responsable)
            ->post('/bandes', $this->donnees(['statut' => 'en_cours', 'exploitation_id' => $autre->id]))
            ->assertRedirect('/bandes');

        $this->assertDatabaseHas('bandes_elevage', [
            'code' => 'B1',
            'statut' => 'planifiee',
            'exploitation_id' => $this->ferme->id,
        ]);
    }

    public function test_un_effectif_superieur_a_la_capacite_est_refuse(): void
    {
        $this->actingAs($this->responsable)
            ->post('/bandes', $this->donnees(['effectif_initial' => 1001]))
            ->assertSessionHasErrors('effectif_initial');

        $this->assertDatabaseCount('bandes_elevage', 0);
    }

    public function test_un_poulailler_en_maintenance_ou_inactif_est_refuse(): void
    {
        $this->actingAs($this->responsable);

        foreach (['maintenance', 'inactif'] as $etat) {
            $this->poulailler->update(['etat' => $etat]);

            $this->post('/bandes', $this->donnees())->assertSessionHasErrors('poulailler_id');
        }

        $this->assertDatabaseCount('bandes_elevage', 0);
    }

    public function test_un_poulailler_d_une_autre_ferme_est_refuse(): void
    {
        $autre = Exploitation::factory()->create();
        $etranger = Poulailler::factory()->create(['exploitation_id' => $autre->id]);

        $this->actingAs($this->responsable)
            ->post('/bandes', $this->donnees(['poulailler_id' => $etranger->id]))
            ->assertSessionHasErrors('poulailler_id');

        $this->assertDatabaseCount('bandes_elevage', 0);
    }

    public function test_une_fin_non_posterieure_au_debut_est_refusee(): void
    {
        $this->actingAs($this->responsable)
            ->post('/bandes', $this->donnees(['date_fin_prevue' => $this->jour(5)]))
            ->assertSessionHasErrors('date_fin_prevue');
    }

    public function test_deux_bandes_ne_peuvent_pas_se_chevaucher_dans_un_poulailler(): void
    {
        $this->bande();

        $this->actingAs($this->responsable);

        $this->post('/bandes', $this->donnees(['code' => 'B2', 'date_debut' => $this->jour(40), 'date_fin_prevue' => $this->jour(80)]))
            ->assertSessionHasErrors('date_debut');
        $this->assertDatabaseMissing('bandes_elevage', ['code' => 'B2']);

        $this->post('/bandes', $this->donnees(['code' => 'B2', 'date_debut' => $this->jour(51), 'date_fin_prevue' => $this->jour(90)]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bandes_elevage', ['code' => 'B2']);
    }

    public function test_une_bande_en_cours_depassee_occupe_encore_son_poulailler(): void
    {
        $existante = $this->bande([
            'statut' => 'en_cours',
            'date_debut' => $this->jour(-60),
            'date_fin_prevue' => $this->jour(-10),
        ]);

        $this->actingAs($this->responsable);

        $this->post('/bandes', $this->donnees(['code' => 'B2', 'date_debut' => $this->jour(5), 'date_fin_prevue' => $this->jour(60)]))
            ->assertSessionHasErrors('date_debut');

        $existante->update(['date_fin_prevue' => $this->jour(20)]);

        $this->post('/bandes', $this->donnees(['code' => 'B2', 'date_debut' => $this->jour(21), 'date_fin_prevue' => $this->jour(60)]))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bandes_elevage', ['code' => 'B2']);
    }

    public function test_les_bandes_terminees_et_annulees_ne_bloquent_pas_le_poulailler(): void
    {
        $this->bande(['statut' => 'terminee', 'date_fin_reelle' => $this->jour(5)]);
        $this->bande(['statut' => 'annulee']);

        $this->actingAs($this->responsable)
            ->post('/bandes', $this->donnees(['code' => 'B9']))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bandes_elevage', ['code' => 'B9']);
    }

    public function test_une_bande_demarre_a_sa_date_de_debut(): void
    {
        $this->actingAs($this->responsable);

        $future = $this->bande();
        $this->post("/bandes/{$future->id}/demarrer")->assertSessionHas('erreur');
        $this->assertSame('planifiee', $future->fresh()->statut);

        $prete = $this->bande(['code' => 'BP', 'date_debut' => $this->jour(-1), 'date_fin_prevue' => $this->jour(40)]);
        $future->update(['statut' => 'annulee']);

        $this->post("/bandes/{$prete->id}/demarrer")->assertRedirect("/bandes/{$prete->id}");
        $this->assertSame('en_cours', $prete->fresh()->statut);
    }

    public function test_une_bande_ne_demarre_pas_dans_un_poulailler_en_maintenance(): void
    {
        $bande = $this->bande(['date_debut' => $this->jour(-1), 'date_fin_prevue' => $this->jour(40)]);
        $this->poulailler->update(['etat' => 'maintenance']);

        $this->actingAs($this->responsable)
            ->post("/bandes/{$bande->id}/demarrer")
            ->assertSessionHas('erreur');

        $this->assertSame('planifiee', $bande->fresh()->statut);
    }

    public function test_les_transitions_interdites_sont_refusees(): void
    {
        $this->actingAs($this->responsable);

        $enCours = $this->bande(['statut' => 'en_cours', 'date_debut' => $this->jour(-10), 'date_fin_prevue' => $this->jour(30)]);
        $this->post("/bandes/{$enCours->id}/demarrer")->assertSessionHas('erreur');
        $this->assertSame('en_cours', $enCours->fresh()->statut);

        $planifiee = $this->bande(['code' => 'BP', 'date_debut' => $this->jour(60), 'date_fin_prevue' => $this->jour(90)]);
        $this->post("/bandes/{$planifiee->id}/terminer", ['date_fin_reelle' => $this->jour(0)])->assertSessionHas('erreur');
        $this->assertSame('planifiee', $planifiee->fresh()->statut);

        $terminee = $this->bande(['code' => 'BT', 'statut' => 'terminee', 'date_debut' => $this->jour(-90), 'date_fin_prevue' => $this->jour(-50), 'date_fin_reelle' => $this->jour(-48)]);
        $this->post("/bandes/{$terminee->id}/annuler")->assertSessionHas('erreur');
        $this->assertSame('terminee', $terminee->fresh()->statut);
    }

    public function test_une_bande_en_cours_se_termine_avec_une_date_valide(): void
    {
        $bande = $this->bande(['statut' => 'en_cours', 'date_debut' => $this->jour(-10), 'date_fin_prevue' => $this->jour(30)]);

        $this->actingAs($this->responsable);

        $this->post("/bandes/{$bande->id}/terminer", ['date_fin_reelle' => $this->jour(3)])
            ->assertSessionHasErrors('date_fin_reelle');

        $this->post("/bandes/{$bande->id}/terminer", ['date_fin_reelle' => $this->jour(-20)])
            ->assertSessionHas('erreur');
        $this->assertSame('en_cours', $bande->fresh()->statut);

        $this->post("/bandes/{$bande->id}/terminer", ['date_fin_reelle' => $this->jour(0)])
            ->assertSessionHas('succes');

        $bande->refresh();
        $this->assertSame('terminee', $bande->statut);
        $this->assertSame($this->jour(0), $bande->date_fin_reelle->toDateString());
    }

    public function test_une_bande_peut_etre_annulee_depuis_planifiee_ou_en_cours(): void
    {
        $this->actingAs($this->responsable);

        $planifiee = $this->bande();
        $this->post("/bandes/{$planifiee->id}/annuler")->assertSessionHas('succes');
        $this->assertSame('annulee', $planifiee->fresh()->statut);

        $enCours = $this->bande(['code' => 'BE', 'statut' => 'en_cours', 'date_debut' => $this->jour(-10), 'date_fin_prevue' => $this->jour(30)]);
        $this->post("/bandes/{$enCours->id}/annuler")->assertSessionHas('succes');
        $this->assertSame('annulee', $enCours->fresh()->statut);
    }

    public function test_une_bande_en_cours_n_accepte_que_la_modification_de_la_fin_prevue(): void
    {
        $bande = $this->bande(['code' => 'BE', 'statut' => 'en_cours', 'date_debut' => $this->jour(-10), 'date_fin_prevue' => $this->jour(30)]);

        $this->actingAs($this->responsable)
            ->put("/bandes/{$bande->id}", ['date_fin_prevue' => $this->jour(90), 'code' => 'ZZ', 'effectif_initial' => 1])
            ->assertRedirect("/bandes/{$bande->id}");

        $bande->refresh();
        $this->assertSame('BE', $bande->code);
        $this->assertSame(500, $bande->effectif_initial);
        $this->assertSame($this->jour(90), $bande->date_fin_prevue->toDateString());
    }

    public function test_une_bande_terminee_est_en_lecture_seule(): void
    {
        $bande = $this->bande([
            'statut' => 'terminee',
            'date_debut' => $this->jour(-60),
            'date_fin_prevue' => $this->jour(-20),
            'date_fin_reelle' => $this->jour(-18),
        ]);

        $this->actingAs($this->responsable);

        $this->get("/bandes/{$bande->id}/modifier")->assertForbidden();
        $this->put("/bandes/{$bande->id}", $this->donnees(['code' => 'ZZ']))->assertForbidden();
        $this->assertNotSame('ZZ', $bande->fresh()->code);
    }

    public function test_une_bande_d_une_autre_ferme_est_introuvable(): void
    {
        $autre = Exploitation::factory()->create();
        $etrangere = BandeElevage::factory()->create(['exploitation_id' => $autre->id]);

        $this->actingAs($this->responsable);

        $this->get("/bandes/{$etrangere->id}")->assertNotFound();
        $this->get("/bandes/{$etrangere->id}/modifier")->assertNotFound();
        $this->post("/bandes/{$etrangere->id}/demarrer")->assertNotFound();
        $this->get('/bandes')->assertOk()->assertDontSee($etrangere->code);
    }

    public function test_un_agent_consulte_les_bandes_sans_pouvoir_les_modifier(): void
    {
        $bande = $this->bande();

        $this->actingAs($this->agent);

        $this->get('/bandes')->assertOk();
        $this->get("/bandes/{$bande->id}")->assertOk();
        $this->get('/bandes/creer')->assertForbidden();
        $this->post('/bandes', $this->donnees())->assertForbidden();
        $this->get("/bandes/{$bande->id}/modifier")->assertForbidden();
        $this->post("/bandes/{$bande->id}/demarrer")->assertForbidden();
        $this->assertSame('planifiee', $bande->fresh()->statut);
    }

    public function test_un_admin_n_a_pas_acces_aux_bandes(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/bandes')
            ->assertForbidden();
    }

    public function test_une_ferme_inactive_ne_peut_pas_creer_de_bande(): void
    {
        $this->ferme->update(['statut' => 'inactive']);

        $this->actingAs($this->responsable);

        $this->get('/bandes')->assertOk();
        $this->post('/bandes', $this->donnees())->assertSessionHas('erreur');
        $this->assertDatabaseCount('bandes_elevage', 0);
    }

    public function test_la_fiche_affiche_l_effectif_vivant(): void
    {
        $bande = $this->bande(['statut' => 'en_cours', 'date_debut' => $this->jour(-10), 'date_fin_prevue' => $this->jour(30)]);
        Mortalite::factory()->create([
            'exploitation_id' => $this->ferme->id,
            'bande_id' => $bande->id,
            'quantite' => 20,
            'date_mortalite' => $this->jour(-2),
        ]);

        $this->actingAs($this->responsable)
            ->get("/bandes/{$bande->id}")
            ->assertOk()
            ->assertSee('480');
    }

    public function test_la_capacite_d_un_poulailler_ne_descend_pas_sous_l_effectif_present(): void
    {
        $bande = $this->bande(['statut' => 'en_cours', 'date_debut' => $this->jour(-10), 'date_fin_prevue' => $this->jour(30)]);
        Mortalite::factory()->create([
            'exploitation_id' => $this->ferme->id,
            'bande_id' => $bande->id,
            'quantite' => 100,
            'date_mortalite' => $this->jour(-2),
        ]);

        $donnees = ['code' => $this->poulailler->code, 'nom' => 'N', 'etat' => 'actif'];

        $this->actingAs($this->responsable);

        $this->put("/poulaillers/{$this->poulailler->id}", $donnees + ['capacite' => 399])
            ->assertSessionHasErrors('capacite');
        $this->assertSame(1000, $this->poulailler->fresh()->capacite);

        $this->put("/poulaillers/{$this->poulailler->id}", $donnees + ['capacite' => 400])
            ->assertSessionHasNoErrors();
        $this->assertSame(400, $this->poulailler->fresh()->capacite);
    }
}