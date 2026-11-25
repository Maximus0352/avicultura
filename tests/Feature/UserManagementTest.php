<?php

namespace Tests\Feature;

use App\Models\Exploitation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    private function donnees(array $surcharge = []): array
    {
        return array_merge([
            'nom' => 'Agent Test',
            'email' => 'agent.test@ferme.test',
            'password' => 'Motdepasse1',
        ], $surcharge);
    }

    public function test_un_responsable_cree_un_agent_dans_sa_ferme(): void
    {
        $ferme = Exploitation::factory()->create();
        $autre = Exploitation::factory()->create();
        $responsable = User::factory()->responsable()->create(['exploitation_id' => $ferme->id]);

        $this->actingAs($responsable)
            ->post('/utilisateurs', $this->donnees(['role' => 'admin', 'exploitation_id' => $autre->id]))
            ->assertRedirect('/utilisateurs');

        $agent = User::where('email', 'agent.test@ferme.test')->firstOrFail();
        $this->assertSame('agent', $agent->role);
        $this->assertSame($ferme->id, $agent->exploitation_id);
        $this->assertTrue($agent->actif);
    }

    public function test_un_admin_cree_un_responsable_dans_la_ferme_choisie(): void
    {
        $ferme = Exploitation::factory()->create();
        $this->actingAs(User::factory()->admin()->create());

        $this->post('/utilisateurs', $this->donnees(['exploitation_id' => $ferme->id]))
            ->assertRedirect('/utilisateurs');

        $cree = User::where('email', 'agent.test@ferme.test')->firstOrFail();
        $this->assertSame('responsable', $cree->role);
        $this->assertSame($ferme->id, $cree->exploitation_id);
    }

    public function test_un_admin_doit_choisir_une_exploitation(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->post('/utilisateurs', $this->donnees())->assertSessionHasErrors('exploitation_id');

        $this->assertDatabaseMissing('users', ['email' => 'agent.test@ferme.test']);
    }

    public function test_un_utilisateur_d_une_autre_ferme_est_introuvable(): void
    {
        $ferme = Exploitation::factory()->create();
        $autre = Exploitation::factory()->create();
        $responsable = User::factory()->responsable()->create(['exploitation_id' => $ferme->id]);
        $etranger = User::factory()->agent()->create(['exploitation_id' => $autre->id]);

        $this->actingAs($responsable);

        $this->get("/utilisateurs/{$etranger->id}/modifier")->assertNotFound();
        $this->patch("/utilisateurs/{$etranger->id}/statut")->assertNotFound();
        $this->get('/utilisateurs')->assertOk()->assertDontSee($etranger->email);
        $this->assertTrue($etranger->fresh()->actif);
    }

    public function test_un_responsable_ne_peut_pas_se_desactiver_lui_meme(): void
    {
        $responsable = User::factory()->responsable()->create();

        $this->actingAs($responsable)
            ->patch("/utilisateurs/{$responsable->id}/statut")
            ->assertForbidden();

        $this->assertTrue($responsable->fresh()->actif);
    }

    public function test_un_agent_ne_peut_pas_gerer_les_utilisateurs(): void
    {
        $this->actingAs(User::factory()->agent()->create());

        $this->get('/utilisateurs')->assertForbidden();
        $this->post('/utilisateurs', $this->donnees())->assertForbidden();
    }

    public function test_un_agent_desactive_ne_peut_plus_se_connecter(): void
    {
        $ferme = Exploitation::factory()->create();
        $responsable = User::factory()->responsable()->create(['exploitation_id' => $ferme->id]);
        $agent = User::factory()->agent()->create(['exploitation_id' => $ferme->id, 'password' => 'Motdepasse1']);

        $this->actingAs($responsable)->patch("/utilisateurs/{$agent->id}/statut");
        $this->assertFalse($agent->fresh()->actif);

        Auth::logout();

        $this->post('/connexion', ['email' => $agent->email, 'password' => 'Motdepasse1'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_une_ferme_inactive_est_en_lecture_seule(): void
    {
        $ferme = Exploitation::factory()->inactive()->create();
        $responsable = User::factory()->responsable()->create(['exploitation_id' => $ferme->id]);

        $this->actingAs($responsable);

        $this->get('/utilisateurs')->assertOk();
        $this->post('/utilisateurs', $this->donnees())->assertSessionHas('erreur');

        $this->assertDatabaseMissing('users', ['email' => 'agent.test@ferme.test']);
    }

    public function test_role_et_exploitation_ne_sont_pas_remplissables_en_masse(): void
    {
        $utilisateur = new User();

        $this->assertFalse($utilisateur->isFillable('role'));
        $this->assertFalse($utilisateur->isFillable('exploitation_id'));
    }
}