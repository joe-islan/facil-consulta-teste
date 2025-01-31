<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ControllerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medico\StoreMedicoRequest;
use App\Services\MedicoService;
use App\Services\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class MedicoController extends Controller
{
    public function __construct(
        private MedicoService $medicoService,
        private PacienteService $pacienteService,
        private Logger $logger,
        private ControllerHelper $helper,
    ) {
        $this->middleware('auth:api', ['except' => ['index', 'doctorsByCity']]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/medicos",
     *     summary="Lista todos os médicos",
     *     description="Retorna a lista de médicos cadastrados no sistema.",
     *     tags={"Médicos"},
     *
     *     @OA\Parameter(
     *         name="nome",
     *         in="query",
     *         description="Filtrar médicos pelo nome",
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de médicos recuperada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de médicos recuperada com sucesso"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/cidades/{cidade_id}/medicos",
     *     summary="Lista médicos por cidade",
     *     description="Retorna a lista de médicos em uma cidade específica.",
     *     tags={"Médicos"},
     *
     *     @OA\Parameter(
     *         name="cidade_id",
     *         in="path",
     *         description="ID da cidade",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de médicos recuperada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de médicos por cidade recuperada com sucesso"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function doctorsByCity(Request $request, int $cidade_id): JsonResponse
    {
        try {
            $medicos = $this->medicoService->findByCity($cidade_id, $request->query('nome'));

            return $this->helper->successJsonResponse('Lista de médicos por cidade recuperada com sucesso', $medicos);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar médicos por cidade', ['erro' => $e->getMessage()]);

            return $this->helper->errorJsonResponse('Erro interno ao listar médicos por cidade', null, 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/medicos",
     *     summary="Cadastra um novo médico",
     *     description="Cria um novo cadastro de médico no sistema.",
     *     tags={"Médicos"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"nome", "especialidade"},
     *
     *             @OA\Property(property="nome", type="string", example="Dr. João Silva"),
     *             @OA\Property(property="especialidade", type="string", example="Cardiologia")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Médico cadastrado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Médico cadastrado com sucesso"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/medicos/{medicoId}/pacientes",
     *     summary="Lista os pacientes de um médico",
     *     description="Retorna a lista de pacientes de um médico específico.",
     *     tags={"Médicos"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="medicoId",
     *         in="path",
     *         description="ID do médico",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pacientes recuperada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lista de pacientes recuperada com sucesso"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     */
    public function getPatientsByDoctor(Request $request, int $medicoId): JsonResponse
    {
        try {
            $apenasAgendadas = filter_var($request->query('apenas-agendadas'), FILTER_VALIDATE_BOOLEAN);
            $nome = $request->query('nome');
            $pacientes = $this->pacienteService->getByDoctor($medicoId, $apenasAgendadas, $nome);

            return $this->helper->successJsonResponse('Lista de pacientes recuperada com sucesso', $pacientes);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao listar pacientes do médico', ['erro' => $e->getMessage()]);

            return $this->helper->errorJsonResponse('Erro interno ao listar pacientes do médico', null, 500);
        }
    }
}
