<?php

namespace Database\Factories;

use App\Models\Cidade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medico>
 */
class MedicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => 'Dr. ' . $this->faker->name,
            'especialidade' => $this->faker->randomElement(['Cardiologia', 'Dermatologia', 'Pediatria', 'Ortopedia']),
            'cidade_id' => Cidade::inRandomOrder()->first()->id ?? Cidade::factory(),
        ];
    }
}
