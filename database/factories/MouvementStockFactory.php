<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Intrant;
use App\Models\User;
use App\Models\MouvementStock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MouvementStock>
 */
class MouvementStockFactory extends Factory
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
            'user_id' => fn (array $a) => User::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'type' => 'entree',
            'sens_ajustement' => null,
            'quantite' => 100,
            'motif' => null,
            'distribution_id' => null,
        ];
    }
}
