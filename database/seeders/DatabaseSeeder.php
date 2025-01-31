<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cidade;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Consulta;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CidadeSeeder::class,
            MedicoSeeder::class,
            PacienteSeeder::class,
            ConsultaSeeder::class,
            UserSeeder::class,
        ]);
    }
}
