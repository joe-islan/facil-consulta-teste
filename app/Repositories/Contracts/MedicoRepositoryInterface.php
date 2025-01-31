<?php

namespace App\Repositories\Contracts;

use App\Models\Medico;
use Illuminate\Database\Eloquent\Collection;

interface MedicoRepositoryInterface
{
    public function all(?string $nome = null): Collection;
    public function findByCidade(int $cidadeId, ?string $nome = null): Collection;
    public function create(array $data): Medico;
}
