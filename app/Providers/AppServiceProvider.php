<?php

namespace App\Providers;

use App\Repositories\CidadeRepository;
use App\Repositories\ConsultaRepository;
use App\Repositories\Contracts\CidadeRepositoryInterface;
use App\Repositories\Contracts\ConsultaRepositoryInterface;
use App\Repositories\Contracts\MedicoRepositoryInterface;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\MedicoRepository;
use App\Repositories\PacienteRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CidadeRepositoryInterface::class, CidadeRepository::class);
        $this->app->bind(MedicoRepositoryInterface::class, MedicoRepository::class);
        $this->app->bind(PacienteRepositoryInterface::class, PacienteRepository::class);
        $this->app->bind(ConsultaRepositoryInterface::class, ConsultaRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
