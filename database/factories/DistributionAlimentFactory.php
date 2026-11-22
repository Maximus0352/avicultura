<?php

namespace Database\Factories;

use App\Models\BandeElevage;
use App\Models\DistributionAliment;
use App\Models\Exploitation;
use App\Models\Intrant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DistributionAliment>
 */
class DistributionAlimentFactory extends Factory
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
            'intrant_id' => fn (array $a) => Intrant::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'user_id' => fn (array $a) => User::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'date_distribution' => now()->toDateString(),
            'quantite' => 25,
            'observation' => null,
        ];
    }
}
