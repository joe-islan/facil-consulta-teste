<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Paciente\StorePacienteRequest;
use App\Http\Requests\Paciente\UpdatePacienteRequest;
use App\Services\PacienteService;
use App\Helpers\ControllerHelper;
use Illuminate\Log\Logger;
use Illuminate\Http\JsonResponse;

class PacienteController extends Controller
{
    public function __construct(
        private PacienteService $pacienteService,
        private Logger $logger,
        private ControllerHelper $helper
    ) {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        try {
            $pacientes = $this->pacienteService->listAll();
            return $this->helper->successJsonResponse('Lista de pacientes recuperada com sucesso', $pacientes);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar pacientes', ['erro' => $e->getMessage()]);
            return $this->helper->errorJsonResponse('Erro interno ao listar pacientes', null, 500);
        }
    }

    public function store(StorePacienteRequest $request): JsonResponse
    {
        try {
            $paciente = $this->pacienteService->create($request->validated());
            return $this->helper->successJsonResponse('Paciente cadastrado com sucesso', $paciente, 201);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao cadastrar paciente', ['erro' => $e->getMessage(), 'dados' => $request->all()]);
            return $this->helper->errorJsonResponse('Erro interno ao cadastrar paciente', null, 500);
        }
    }

    public function update(UpdatePacienteRequest $request, int $id): JsonResponse
    {
        try {
            $paciente = $this->pacienteService->update($id, $request->validated());
            return $this->helper->successJsonResponse('Paciente atualizado com sucesso', $paciente);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao atualizar paciente', ['erro' => $e->getMessage(), 'dados' => $request->all()]);
            return $this->helper->errorJsonResponse('Erro interno ao atualizar paciente', null, 500);
        }
    }
}
