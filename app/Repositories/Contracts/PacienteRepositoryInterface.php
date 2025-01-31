<?php

namespace App\Repositories\Contracts;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Collection;

interface PacienteRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): Paciente;

    public function update(int $id, array $data): Paciente;

    public function getByDoctor(int $medicoId, ?bool $apenasAgendadas = null, ?string $nome = null): Collection;
}
