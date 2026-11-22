<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Poulailler;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poulailler>
 */
class PoulaillerFactory extends Factory
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
            'code' => 'P' . fake()->unique()->numberBetween(1, 9999),
            'nom' => 'Poulailler ' . fake()->word(),
            'capacite' => 1000,
            'localisation' => fake()->streetName(),
            'etat' => 'actif',
        ];
    }
}
