<?php

namespace App\Repositories;

use App\Models\Medico;
use App\Repositories\Contracts\MedicoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MedicoRepository implements MedicoRepositoryInterface
{
    public function all(?string $nome = null): Collection
    {
        if ($nome) {
            $nome = preg_replace('/\b(Dra?|Dr)\.? /i', '', $nome);
        }

        return Medico::when($nome, function ($query, $nome) {
            return $query->whereRaw("LOWER(REPLACE(REPLACE(nome, 'Dr. ', ''), 'Dra. ', '')) LIKE LOWER(?)", ["%{$nome}%"]);
        })
            ->orderByRaw("LOWER(REPLACE(REPLACE(nome, 'Dr. ', ''), 'Dra. ', '')) ASC")
            ->get();
    }

    public function findByCidade(int $cidadeId, $nome = null): Collection
    {
        if ($nome) {
            $nome = preg_replace('/\b(Dra?|Dr)\.? /i', '', $nome);
        }

        return Medico::when($nome, function ($query, $nome) {
            return $query->whereRaw("LOWER(REPLACE(REPLACE(nome, 'Dr. ', ''), 'Dra. ', '')) LIKE LOWER(?)", ["%{$nome}%"]);
        })->where('cidade_id', $cidadeId)
            ->orderByRaw("LOWER(REPLACE(REPLACE(nome, 'Dr. ', ''), 'Dra. ', '')) ASC")
            ->get();
    }

    public function create(array $data): Medico
    {
        return Medico::create($data);
    }
}
