<?php

namespace Database\Factories;

use App\Models\Exploitation;
use App\Models\HistoriqueModification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HistoriqueModification>
 */
class HistoriqueModificationFactory extends Factory
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
            'user_id' => fn (array $a) => User::factory()->create(['exploitation_id' => $a['exploitation_id']])->id,
            'table_concernee' => 'mortalites',
            'enregistrement_id' => 1,
            'anciennes_valeurs' => ['quantite' => 5],
            'nouvelles_valeurs' => ['quantite' => 4],
            'date_modification' => now(),
        ];
    }
}
