<?php

namespace App\Services;

use App\Repositories\Contracts\CidadeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CidadeService
{
    protected $cidadeRepository;

    public function __construct(CidadeRepositoryInterface $cidadeRepository)
    {
        $this->cidadeRepository = $cidadeRepository;
    }

    public function listAll(?string $nome = null): Collection
    {
        return $this->cidadeRepository->all($nome);
    }
}
