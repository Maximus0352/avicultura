<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Poulailler;
use App\Models\Production;
use Illuminate\Database\Eloquent\Factories\Factory;

class BandeElevageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'poulailler_id' => fn (array $attributes) => Poulailler::factory()
                ->create(['exploitation_id' => $attributes['exploitation_id']])->id,
            'production_id' => fn (array $attributes) => Production::factory()
                ->create(['exploitation_id' => $attributes['exploitation_id']])->id,
            'code' => 'B' . fake()->unique()->numberBetween(1, 99999),
            'date_debut' => now()->subDays(10)->toDateString(),
            'date_fin_prevue' => now()->addDays(35)->toDateString(),
            'effectif_initial' => 500,
            'statut' => 'en_cours',
        ];
    }
}