<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\Production;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Production>
 */
class ProductionFactory extends Factory
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
            'nom' => 'Poulet de chair ' . fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->sentence(),
            'unite_production' => 'sujet',
        ];
    }
}
