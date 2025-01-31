<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consulta\StoreConsultaRequest;
use App\Http\Requests\Consulta\UpdateConsultaRequest;
use App\Services\ConsultaService;
use App\Helpers\ControllerHelper;
use Illuminate\Log\Logger;
use Illuminate\Http\JsonResponse;

class ConsultaController extends Controller
{
    public function __construct(
        private ConsultaService $consultaService,
        private Logger $logger,
        private ControllerHelper $helper
    ) {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        try {
            $consultas = $this->consultaService->listAll();
            return $this->helper->successJsonResponse('Lista de consultas recuperada com sucesso', $consultas);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar consultas', ['erro' => $e->getMessage()]);
            return $this->helper->errorJsonResponse('Erro interno ao listar consultas', null, 500);
        }
    }

    public function store(StoreConsultaRequest $request): JsonResponse
    {
        try {
            $consulta = $this->consultaService->create($request->validated());
            return $this->helper->successJsonResponse('Consulta agendada com sucesso', $consulta, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->helper->errorJsonResponse($e->getMessage(), null, 422);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao agendar consulta', ['erro' => $e->getMessage(), 'dados' => $request->all()]);
            return $this->helper->errorJsonResponse('Erro interno ao agendar consulta', null, 500);
        }
    }

    public function update(UpdateConsultaRequest $request, int $id): JsonResponse
    {
        try {
            $consulta = $this->consultaService->update($id, $request->validated());
            return $this->helper->successJsonResponse('Consulta atualizada com sucesso', $consulta);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao atualizar consulta', ['erro' => $e->getMessage(), 'dados' => $request->all()]);
            return $this->helper->errorJsonResponse('Erro interno ao atualizar consulta', null, 500);
        }
    }
}
