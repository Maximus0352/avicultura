<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'actif', 'role:admin,responsable'])
            ->get('/test-reserve', fn () => 'ok');
    }

    public function test_un_role_autorise_accede_a_la_route(): void
    {
        $this->actingAs(User::factory()->responsable()->create())
            ->get('/test-reserve')
            ->assertOk();
    }

    public function test_un_agent_recoit_403(): void
    {
        $this->actingAs(User::factory()->agent()->create())
            ->get('/test-reserve')
            ->assertForbidden();
    }

    public function test_un_visiteur_est_redirige_avant_le_controle_de_role(): void
    {
        $this->get('/test-reserve')->assertRedirect('/connexion');
    }
}