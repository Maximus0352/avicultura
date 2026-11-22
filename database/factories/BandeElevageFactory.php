<?php

namespace Database\Factories;

use App\Models\BandeElevage;
use App\Models\Exploitation;
use App\Models\Poulailler;
use App\Models\Production;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BandeElevage>
 */
class BandeElevageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $exploitation = Exploitation::factory();

        return [
            'exploitation_id' => $exploitation,
            'poulailler_id' => Poulailler::factory()->for($exploitation),
            'production_id' => Production::factory()->for($exploitation),
            'code' => 'B' . fake()->unique()->numberBetween(1, 99999),
            'date_debut' => now()->subDays(10)->toDateString(),
            'date_fin_prevue' => now()->addDays(35)->toDateString(),
            'effectif_initial' => 500,
            'statut' => 'en_cours',
        ];
    }
}
