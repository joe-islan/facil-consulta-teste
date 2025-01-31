<?php

namespace App\Repositories\Contracts;

use App\Models\Consulta;
use Illuminate\Database\Eloquent\Collection;

interface ConsultaRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): Consulta;

    public function update(int $id, array $data): Consulta;

    public function existsConsultaNoMesmoHorario(int $medicoId, string $data): bool;
}
