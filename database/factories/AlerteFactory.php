<?php

namespace Database\Factories;

use App\Models\Alerte;
use App\Models\Exploitation;
use App\Models\Intrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alerte>
 */
class AlerteFactory extends Factory
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
            'intrant_id' => fn (array $a) => Intrant::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'type' => 'stock_critique',
            'message' => 'Stock critique',
            'statut' => 'non_lue',
            'date_alerte' => now(),
            'date_resolution' => null,
        ];
    }

    public function resolue(): static
    {
        return $this->state(['statut' => 'resolue', 'date_resolution' => now()]);
    }
}
