<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KahootGame>
 */
class KahootGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_concurso' => fake()->sentence(3),
            'fecha_celebracion' => fake()->date(),
            'numero_participantes' => fake()->numberBetween(3, 50),
        ];
    }
}
