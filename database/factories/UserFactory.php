<?php

namespace Database\Factories;

use App\Models\Exploitation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exploitation_id' => Exploitation::factory(),
            'nom' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'agent',
            'actif' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(['exploitation_id' => null, 'role' => 'admin']);
    }

    public function responsable(): static
    {
        return $this->state(['role' => 'responsable']);
    }

    public function agent(): static
    {
        return $this->state(['role' => 'agent']);
    }

    public function inactif(): static
    {
        return $this->state(['actif' => false]);
    }
}