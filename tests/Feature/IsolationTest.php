<?php

namespace Tests\Feature;

use App\Models\BandeElevage;
use App\Models\Exploitation;
use App\Models\Intrant;
use App\Models\Poulailler;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IsolationTest extends TestCase
{
    use DatabaseTransactions;

    private Exploitation $exploitationA;
    private Exploitation $exploitationB;
    private User $responsableA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exploitationA = Exploitation::factory()->create();
        $this->exploitationB = Exploitation::factory()->create();
        $this->responsableA = User::factory()->responsable()->create(['exploitation_id' => $this->exploitationA->id]);
    }

    public function test_un_utilisateur_ne_voit_que_les_donnees_de_son_exploitation(): void
    {
        Poulailler::factory()->create(['exploitation_id' => $this->exploitationA->id]);
        Poulailler::factory()->count(2)->create(['exploitation_id' => $this->exploitationB->id]);

        $this->actingAs($this->responsableA);

        $this->assertCount(1, Poulailler::all());
    }

    public function test_une_ressource_etrangere_est_introuvable(): void
    {
        $bandeB = BandeElevage::factory()->create(['exploitation_id' => $this->exploitationB->id]);

        $this->actingAs($this->responsableA);

        $this->assertNull(BandeElevage::find($bandeB->id));
    }

    public function test_exploitation_id_est_impose_a_la_creation(): void
    {
        $this->actingAs($this->responsableA);

        $intrant = Intrant::create([
            'exploitation_id' => $this->exploitationB->id,
            'nom' => 'Aliment test',
            'categorie' => 'aliment',
            'unite' => 'kg',
        ]);

        $this->assertSame($this->exploitationA->id, $intrant->exploitation_id);
    }

    public function test_un_admin_ne_voit_aucune_donnee_metier(): void
    {
        Poulailler::factory()->create(['exploitation_id' => $this->exploitationA->id]);

        $this->actingAs(User::factory()->admin()->create());

        $this->assertCount(0, Poulailler::all());
    }

    public function test_sans_utilisateur_connecte_le_scope_ne_filtre_pas(): void
    {
        Poulailler::factory()->create(['exploitation_id' => $this->exploitationA->id]);
        Poulailler::factory()->create(['exploitation_id' => $this->exploitationB->id]);

        $this->assertCount(2, Poulailler::all());
    }
}