<?php

namespace App\Services;

use App\Models\Medico;
use App\Repositories\Contracts\MedicoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MedicoService
{
    protected $medicoRepository;

    public function __construct(MedicoRepositoryInterface $medicoRepository)
    {
        $this->medicoRepository = $medicoRepository;
    }

    public function listAll(?string $nome = null): Collection
    {
        return $this->medicoRepository->all($nome);
    }

    public function findByCity(int $cidadeId, $nome = null): Collection
    {
        return $this->medicoRepository->findByCity($cidadeId, $nome);
    }

    public function create(array $data): Medico
    {
        return $this->medicoRepository->create($data);
    }
}
