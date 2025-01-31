<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medico\StoreMedicoRequest;
use App\Services\MedicoService;
use App\Services\PacienteService;
use App\Helpers\ControllerHelper;
use Illuminate\Log\Logger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function __construct(
        private MedicoService $medicoService,
        private PacienteService $pacienteService,
        private Logger $logger,
        private ControllerHelper $helper
    ) {
        $this->middleware('auth:api', ['except' => ['index', 'medicosPorCidade']]);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $medicos = $this->medicoService->listAll($request->query('nome'));
            return $this->helper->successJsonResponse('Lista de médicos recuperada com sucesso', $medicos);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar médicos', ['erro' => $e->getMessage()]);
            return $this->helper->errorJsonResponse('Erro interno ao listar médicos', null, 500);
        }
    }

    public function medicosPorCidade(Request $request, int $cidade_id): JsonResponse
    {
        try {
            $medicos = $this->medicoService->findByCidade($cidade_id, $request->query('nome'));
            return $this->helper->successJsonResponse('Lista de médicos por cidade recuperada com sucesso', $medicos);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar médicos por cidade', ['erro' => $e->getMessage()]);
            return $this->helper->errorJsonResponse('Erro interno ao listar médicos por cidade', null, 500);
        }
    }

    public function store(StoreMedicoRequest $request): JsonResponse
    {
        try {
            $medico = $this->medicoService->create($request->validated());
            return $this->helper->successJsonResponse('Médico cadastrado com sucesso', $medico, 201);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao cadastrar médico', ['erro' => $e->getMessage(), 'dados' => $request->all()]);
            return $this->helper->errorJsonResponse('Erro interno ao cadastrar médico', null, 500);
        }
    }

    public function getPacientesByMedico(Request $request, int $medicoId): JsonResponse
    {
        try {
            $apenasAgendadas = filter_var($request->query('apenas-agendadas'), FILTER_VALIDATE_BOOLEAN);
            $nome = $request->query('nome');
            $pacientes = $this->pacienteService->getByMedico($medicoId, $apenasAgendadas, $nome);
            return $this->helper->successJsonResponse('Lista de pacientes recuperada com sucesso', $pacientes);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar pacientes do médico', ['erro' => $e->getMessage()]);
            return $this->helper->errorJsonResponse('Erro interno ao listar pacientes do médico', null, 500);
        }
    }
}
