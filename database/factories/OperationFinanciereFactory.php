<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\OperationFinanciere;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OperationFinanciere>
 */
class OperationFinanciereFactory extends Factory
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
            'bande_id' => null,
            'activite_id' => null,
            'vente_id' => null,
            'user_id' => fn (array $a) => User::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'type' => 'depense',
            'categorie' => 'aliment',
            'montant' => 100000,
            'date_operation' => now()->toDateString(),
            'description' => fake()->sentence(),
        ];
    }

    public function recette(): static
    {
        return $this->state(['type' => 'recette', 'categorie' => 'autre']);
    }
}
