<?php

namespace App\Repositories;

use App\Models\Paciente;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PacienteRepository implements PacienteRepositoryInterface
{
    public function all(): Collection
    {
        return Paciente::orderBy('nome', 'asc')->get();
    }

    public function create(array $data): Paciente
    {
        return Paciente::create($data);
    }

    public function update(int $id, array $data): Paciente
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update($data);

        return $paciente;
    }

    public function getByDoctor(int $medicoId, ?bool $apenasAgendadas = null, ?string $nome = null): Collection
    {
        return Paciente::whereHas('consultas', function ($query) use ($medicoId, $apenasAgendadas) {
            $query->where('medico_id', $medicoId); // Filtra pelo médico da rota

            if ($apenasAgendadas) {
                $query->where('data', '>=', now()); // Filtra consultas futuras
            }
        })
            ->when($nome, function ($query, $nome) {
                return $query->where('nome', 'like', "%{$nome}%"); // Busca por nome do paciente
            })
            ->with(['consultas' => function ($query) use ($medicoId, $apenasAgendadas) {
                $query->where('medico_id', $medicoId) // Mantém apenas consultas do médico correto
                    ->when($apenasAgendadas, function ($query) {
                        $query->where('data', '>=', now()); // Mantém apenas consultas futuras se necessário
                    })
                    ->orderBy('data', 'asc'); // Ordena as consultas por data crescente
            }])
            ->orderByRaw('(SELECT MIN(data) FROM consultas WHERE consultas.paciente_id = pacientes.id AND consultas.medico_id = ?) ASC', [$medicoId])
            ->get();
    }
}
