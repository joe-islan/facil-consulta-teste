<?php

namespace App\Repositories;

use App\Models\Cidade;
use App\Repositories\Contracts\CidadeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CidadeRepository implements CidadeRepositoryInterface
{
    public function all(?string $nome = null): Collection
    {
        return Cidade::when($nome, function ($query, $nome) {
            return $query->where('nome', 'like', "%{$nome}%");
        })->orderBy('nome', 'asc')->get();
    }
}
