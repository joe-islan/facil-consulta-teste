<?php

namespace App\Repositories;

use App\Models\Consulta;
use App\Repositories\Contracts\ConsultaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConsultaRepository implements ConsultaRepositoryInterface
{
    public function all(): Collection
    {
        return Consulta::with(['medico', 'paciente'])->orderBy('data')->get();
    }

    public function create(array $data): Consulta
    {
        return Consulta::create($data);
    }

    public function update(int $id, array $data): Consulta
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->update($data);

        return $consulta;
    }

    public function existsAppointmentAtSameTime(int $medicoId, string $data): bool
    {
        return Consulta::where('medico_id', $medicoId)
            ->whereBetween('data', [
                now()->parse($data)->subMinutes(14),
                now()->parse($data)->addMinutes(14),
            ])
            ->exists();
    }
}
