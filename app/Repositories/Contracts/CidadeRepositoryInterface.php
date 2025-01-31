<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CidadeRepositoryInterface
{
    public function all(?string $nome = null): Collection;
}
