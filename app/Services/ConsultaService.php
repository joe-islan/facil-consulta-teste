<?php

namespace App\Services;

use App\Models\Consulta;
use App\Repositories\Contracts\ConsultaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConsultaService
{
    protected $consultaRepository;

    public function __construct(ConsultaRepositoryInterface $consultaRepository)
    {
        $this->consultaRepository = $consultaRepository;
    }

    public function listAll(): Collection
    {
        return $this->consultaRepository->all();
    }

    public function create(array $data): Consulta
    {
        // Verifica se já existe uma consulta para esse médico no mesmo horário
        $existeConsulta = $this->consultaRepository->existsConsultaNoMesmoHorario($data['medico_id'], $data['data']);

        if ($existeConsulta) {
            throw \Illuminate\Validation\ValidationException::withMessages(['data' => ['O médico já possui uma consulta marcada nesse horário. Escolha um horário com pelo menos 15 minutos de diferença.']]);
        }

        return $this->consultaRepository->create($data);
    }

    public function update(int $id, array $data): Consulta
    {
        return $this->consultaRepository->update($id, $data);
    }
}
