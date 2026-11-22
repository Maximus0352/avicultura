<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Intrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Intrant>
 */
class IntrantFactory extends Factory
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
            'nom' => 'Aliment ' . fake()->unique()->numberBetween(1, 99999),
            'categorie' => 'aliment',
            'unite' => 'kg',
            'stock_actuel' => 500,
            'seuil_critique' => 50,
            'prix_unitaire' => 2500,
            'actif' => true,
        ];
    }

    public function critique(): static
    {
        return $this->state(['stock_actuel' => 10, 'seuil_critique' => 50]);
    }

    public function categorie(string $categorie): static
    {
        return $this->state(['categorie' => $categorie]);
    }
}
