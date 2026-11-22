<?php

namespace Database\Factories;

use App\Models\BandeElevage;
use App\Models\Exploitation;
use App\Models\User;
use App\Models\Mortalite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mortalite>
 */
class MortaliteFactory extends Factory
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
            'date_mortalite' => now()->toDateString(),
            'quantite' => 5,
            'motif' => 'maladie',
            'observation' => null,
        ];
    }
}
