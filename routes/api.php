<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CidadeController;
use App\Http\Controllers\Api\V1\ConsultaController;
use App\Http\Controllers\Api\V1\MedicoController;
use App\Http\Controllers\Api\V1\PacienteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // 📌 ROTAS DE AUTENTICAÇÃO
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
    });

    // 📌 ROTAS PÚBLICAS✅
    Route::get('cidades', [CidadeController::class, 'index']);
    Route::get('medicos', [MedicoController::class, 'index']);
    Route::get('cidades/{cidade_id}/medicos', [MedicoController::class, 'medicosPorCidade']);

    // 📌 ROTAS PROTEGIDAS (EXIGEM AUTENTICAÇÃO)
    Route::middleware('auth:api')->group(function () {
        // 🔒 Autenticação protegida por JWT
        Route::controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout');
            Route::post('refresh', 'refresh');
            Route::get('user', 'getAuthenticatedUser');
        });

        // 🔒 Médicos (Apenas cadastro protegido)✅
        Route::post('medicos', [MedicoController::class, 'store']);
        Route::get('medicos/{id_medico}/pacientes', [MedicoController::class, 'getPacientesByMedico']);

        // 🔒 Pacientes (Listagem, cadastro e atualização protegidos)
        Route::controller(PacienteController::class)->group(function () {
            Route::get('pacientes', 'index');
            Route::post('pacientes', 'store');
            Route::put('pacientes/{id}', 'update');
        });

        // 🔒 Consultas (Listagem, cadastro e atualização protegidos)
        Route::controller(ConsultaController::class)->group(function () {
            Route::get('consultas', 'index');
            Route::post('medicos/consulta', 'store');
            Route::put('consultas/{id}', 'update');
        });
    });
});
