<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_est_redirige_vers_la_connexion(): void
    {
        $this->get('/tableau-de-bord')->assertRedirect('/connexion');
    }

    public function test_un_utilisateur_actif_peut_se_connecter(): void
    {
        $utilisateur = User::factory()->responsable()->create(['password' => 'Motdepasse1']);

        $this->post('/connexion', ['email' => $utilisateur->email, 'password' => 'Motdepasse1'])
            ->assertRedirect('/tableau-de-bord');

        $this->assertAuthenticatedAs($utilisateur);
    }

    public function test_un_compte_desactive_ne_peut_pas_se_connecter(): void
    {
        $utilisateur = User::factory()->inactif()->create(['password' => 'Motdepasse1']);

        $this->post('/connexion', ['email' => $utilisateur->email, 'password' => 'Motdepasse1'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_mauvais_mot_de_passe_est_refuse(): void
    {
        $utilisateur = User::factory()->create(['password' => 'Motdepasse1']);

        $this->post('/connexion', ['email' => $utilisateur->email, 'password' => 'incorrect'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_compte_desactive_en_session_est_deconnecte(): void
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $utilisateur->update(['actif' => false]);

        $this->get('/tableau-de-bord')->assertRedirect('/connexion');
        $this->assertGuest();
    }
}