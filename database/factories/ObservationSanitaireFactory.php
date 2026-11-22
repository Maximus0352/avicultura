<?php

namespace Database\Factories;

use App\Models\BandeElevage;
use App\Models\Exploitation;
use App\Models\ObservationSanitaire;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ObservationSanitaire>
 */
class ObservationSanitaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'bande_id' => fn (array $a) => BandeElevage::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'user_id' => fn (array $a) => User::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'contenu_chiffre' => 'contenu-chiffre-factice',
            'date_observation' => now()->toDateString(),
        ];
    }
}
