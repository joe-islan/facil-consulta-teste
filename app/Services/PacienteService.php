<?php

namespace App\Services;

use App\Models\Paciente;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PacienteService
{
    protected $pacienteRepository;

    public function __construct(PacienteRepositoryInterface $pacienteRepository)
    {
        $this->pacienteRepository = $pacienteRepository;
    }

    public function listAll(): Collection
    {
        return $this->pacienteRepository->all();
    }

    public function create(array $data): Paciente
    {
        return $this->pacienteRepository->create($data);
    }

    public function update(int $id, array $data): Paciente
    {
        return $this->pacienteRepository->update($id, $data);
    }

    public function getByDoctor(int $medicoId, ?bool $apenasAgendadas = null, ?string $nome = null): Collection
    {
        return $this->pacienteRepository->getByDoctor($medicoId, $apenasAgendadas, $nome);
    }
}
