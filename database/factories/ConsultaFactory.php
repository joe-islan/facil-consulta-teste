<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consulta>
 */
class ConsultaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medico_id' => Medico::inRandomOrder()->first()->id ?? Medico::factory(),
            'paciente_id' => Paciente::inRandomOrder()->first()->id ?? Paciente::factory(),
            'data' => Carbon::now()->addDays(rand(1, 30)),
        ];
    }
}
